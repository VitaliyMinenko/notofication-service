<?php

namespace App\Services;

use App\Dto\NotificationDto;
use App\Entity\User;
use Psr\Cache\CacheException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Exception;

readonly class ProviderService
{
    public function __construct(
        private UserService        $userService,
        private ContainerInterface $serviceContainer,
        private array              $channels,
        private LoggerInterface    $logger,
        private AdapterInterface   $cache,
        private array              $multipleSend,
        private array              $cacheSettings
    ) {
    }

    /**
     * @throws Exception
     */
    public function process(NotificationDto $notificationDto): void
    {
        $user = $this->getUser($notificationDto->getEmail());
        $this->checkLimit($user);
        $channel = $notificationDto->getChannel();
        $message = $notificationDto->getNotification();
        $enabled = filter_var($this->multipleSend['enabled'], FILTER_VALIDATE_BOOLEAN);
        if ($enabled) {
            $this->sendMultiple($message, $user);
        } else {
            $this->sendOne($channel, $message, $user);
        }
    }

    /**
     * @throws Exception
     */
    private function checkLimit(User $user): void
    {
        $email = $user->getEmail();
        $cacheKey = 'notification_limit_' . $user->getId();
        try {
            $cacheItem = $this->cache->getItem($cacheKey);

            if ($cacheItem->isHit()) {
                $logData = $cacheItem->get();
                $currentTimestamp = time();
                $oneHourAgo = $currentTimestamp - $this->cacheSettings['ttl'];
                $recentAttempts = array_filter($logData, fn($timestamp) => $timestamp > $oneHourAgo);

                if (count($recentAttempts) >= $this->cacheSettings['max_attempts']) {
                    $this->logger->info("Too many attempts per hour for user: $email.");
                    throw new Exception('Too many attempts per hour.');
                }

                $logData = array_merge($recentAttempts, [$currentTimestamp]);
            } else {
                $logData = [time()];
            }

            $cacheItem->set($logData);
            $this->cache->save($cacheItem);
        } catch (CacheException $e) {
            $this->logger->error('Cache exception: ' . $e->getMessage());
            throw new Exception('Cache exception: ' . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    private function sendOne(string $channel, string $message, User $user): void
    {
        $sent = false;
        $providers = $this->channels[$channel] ?? [];
        $activeProviders = array_filter(
            $providers,
            fn($providerConfig) => filter_var(
                $providerConfig['enabled'],
                FILTER_VALIDATE_BOOLEAN
            )
        );
        $contact = $user->getContact($channel);
        if (empty($activeProviders)) {
            throw new Exception("No active providers available for channel: $channel");
        }

//        shuffle($activeProviders);
        $maxRetries = 1;
        $retryDelay = 1;

        foreach ($activeProviders as $providerConfig) {
            $providerClass = $providerConfig['class'];
            $provider = $this->serviceContainer->get($providerClass);
            $attempt = 0;
            $sent = false;
            //TODO Not good solution, better change to Jobs. Need more time!
            while ($attempt < $maxRetries && !$sent) {
                try {
                    $provider->send($message, $contact);
                    $sent = true;
                    $this->logger->info("Notification sent successfully using provider: $providerClass");
                    break;
                } catch (Exception $e) {
                    $attempt++;
                    $this->logger->error(
                        "Error sending notification using provider: $providerClass. Attempt: $attempt. Error: " .
                        $e->getMessage()
                    );
                    if ($attempt < $maxRetries) {
                        sleep($retryDelay);
                    }
                }
            }

            if ($sent) {
                break;
            }
        }

        if (!$sent) {
            $this->logger->error("Failed to send notification after $maxRetries attempts.");
        }
    }

    /**
     * @throws Exception
     */
    private function sendMultiple(string $message, User $user): void
    {
        $allChannels = $this->multipleSend['config'];

        foreach ($allChannels as $channelName => $enabled) {
            if (filter_var($enabled, FILTER_VALIDATE_BOOLEAN)) {
                $this->sendOne($channelName, $message, $user);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function getUser(string $email): User
    {
        return $this->userService->getUserByEmail($email);
    }
}

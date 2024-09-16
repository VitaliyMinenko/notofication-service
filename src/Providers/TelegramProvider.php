<?php

namespace App\Providers;

use App\Interface\IProvider;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

readonly class TelegramProvider implements IProvider
{
    public function __construct(private HttpClientInterface $client, private array $config)
    {
    }

    /**
     * @throws Exception
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function send(string $notification, string $contact): void
    {
        $this->process($contact, $notification);
    }

    /**
     * @throws Exception
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function process(string $contact, string $notification): void
    {
        try {
            $telegramUrlTemplate = $this->config['telegram_url'];
            $telegramToken = $this->config['telegram_token'];

            $telegramUrl = sprintf($telegramUrlTemplate, $telegramToken);
            $response = $this->client->request('POST', $telegramUrl, [
                'json' => [
                    'chat_id' => $contact,
                    'text' => $notification,
                ],
            ]);
        } catch (TransportExceptionInterface $exception) {
            throw new Exception($exception->getMessage());
        }

        if ($response->getStatusCode() !== 200) {
            throw new Exception('Failed to sand telegram notification');
        }
    }
}

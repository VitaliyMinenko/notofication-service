<?php

namespace App\Providers;

use App\Interface\IProvider;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Vonage\Client\Credentials\Basic;
use Vonage\Client;
use Vonage\SMS\Message\SMS;

readonly class Vonage implements IProvider
{
    public function __construct(
        private array $config
    ) {
    }

    /**
     * @param string $notification
     * @param string $contact
     * @throws Exception
     * @throws ClientExceptionInterface
     */
    public function send(string $notification, string $contact): void
    {
        $this->process($notification, $contact);
    }

    /**
     * @throws Exception
     */
    private function process(string $notification, string $contact): void
    {
        try {
            $basic = new Basic($this->config['key'], $this->config['secret']);
            $client = new Client($basic);
            $client->sms()->send(
                new SMS($contact, $this->config['organization'], $notification)
            );
        } catch (ClientExceptionInterface $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}

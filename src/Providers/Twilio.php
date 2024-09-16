<?php

namespace App\Providers;

use App\Interface\IProvider;
use Exception;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client as TwilioClient;

readonly class Twilio implements IProvider
{
    public function __construct(
        private array $config
    ) {
    }

    /**
     * @throws Exception
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
            $client = new TwilioClient($this->config['account_sid'], $this->config['auth_token']);
            $client->messages
                ->create(
                    $contact,
                    [
                        "from" => $this->config['phone_number'],
                        "body" => $notification
                    ]
                );
        } catch (TwilioException|ConfigurationException $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}

<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class NotificationDto
{
    #[Assert\NotBlank(message: "EmailProvider should not be empty")]
    #[Assert\Email(message: "Invalid email address")]
    private string $email;

    #[Assert\NotBlank(message: "Notification should not be empty")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "The notification must be at least {{ limit }} characters long",
        maxMessage: "The notification cannot be longer than {{ limit }} characters"
    )]
    private string $notification;

    #[Assert\Type(type: 'string', message: "Notification channel must be a string")]
    #[Assert\Choice(['sms', 'telegram', 'email'])]
    private string $channel;

    public function __construct(
        string $email,
        string $notification,
        string $provider
    ) {
        $this->email = $email;
        $this->notification = $notification;
        $this->channel = $provider;
    }

// Getters and setters, if needed
    public function getEmail(): string
    {
        return $this->email;
    }

    public function getNotification(): string
    {
        return $this->notification;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }
}

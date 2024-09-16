<?php

namespace App\Interface;

use App\Dto\NotificationDto;

interface IProvider
{
    public function send(string $notification, string $contact): void;
}

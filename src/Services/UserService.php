<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\DBAL\Exception;

class UserService
{
    /**
     * @throws Exception
     */
    public function getUserByEmail(string $email): User
    {
        //Getting mock of user or throw error if undefined
        if ($email !== 's.goodman@gmail.com') {
            throw new Exception('Undefined user.');
        }
        $user = new User();
        $user->setId('14141235424');
        $user->setName('Saul Goodman');
        $user->setEmail('s.goodman@gmail.com');
        $user->setPhone('+1236548664');
        $user->setTelegram('371567173');

        return $user;
    }
}

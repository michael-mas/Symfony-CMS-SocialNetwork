<?php

namespace App\Event;

use App\Entity\Users;
use Symfony\Contracts\EventDispatcher\Event;

class ListAllUsersEvent extends Event
{
    const LIST_ALL_Users_EVENT = 'users.list_alls';

    public function __construct(private int $nbUsers) {}

    public function getNbUsers(): int {
        return $this->nbUsers;
    }

}
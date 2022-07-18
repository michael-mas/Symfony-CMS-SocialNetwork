<?php

namespace App\Event;

use App\Entity\Pages;
use Symfony\Contracts\EventDispatcher\Event;

class ListAllPagesEvent extends Event
{
    const LIST_ALL_Pages_EVENT = 'pages.list.alls';

    public function __construct(private int $nbPages) {}

    public function getNbPages(): int {
        return $this->nbPages;
    }

}
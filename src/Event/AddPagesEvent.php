<?php

namespace App\Event;

use App\Entity\Pages;
use Symfony\Contracts\EventDispatcher\Event;


class AddPagesEvent extends Event
{
    const ADD_PAGES_EVENT = 'pages.add';

    public function __construct(private Produits $produits) {}

    public function getPages(): Pages {
        return $this->page;
    }

}
<?php

namespace App\Event;

use App\Event\ListAllPersonnesEvent;
use Symfony\Contracts\EventDispatcher\Event;

class ListAllPersonnesEvent extends Event
{
    const LIST_ALL_PERSONNE_EVENT = 'personne.alls';

    public function __construct(private int $nbPersonne)
    {}

    public function getNbPersonne(): int
    {
        return $this->nbPersonne;
    }
}
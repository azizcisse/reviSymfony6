<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonnesEvent;
use App\EventListener\PersonneListener;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class PersonneListener
{
    public function __construct(private LoggerInterface $logger)
    {}
   public function onPersonneAdd(AddPersonneEvent $event)
   {
       $this->logger->debug("Salam je suis entrain d'écouter l'évenement personne.add et une personne vient d'etre ajouté et c'est". $event->getPersonne()->getName());
   } 

   public function onListAllPersonnes(ListAllPersonnesEvent $event)
   {
       $this->logger->debug("Salam Le nombre de personne dans la base est". $event->getNbPersonne());
   } 

   public function onListAllPersonnes2(ListAllPersonnesEvent $event)
   {
       $this->logger->debug("Salam Le second Listener avec le nombre :". $event->getNbPersonne());
   } 

   public function logKernelRequest(KernelEvent $event)
   {
       dd($event->getRequest());
   } 
}
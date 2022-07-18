<?php

namespace App\EventListener;

use App\Event\ListAllUsersEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class UsersListener
{
    public function __construct(private LoggerInterface $logger) {
}
   
    public function onListAllUsers(ListAllUsersEvent $event){
        $this->logger->debug("Le nombre de Users dans la base est ". $event->getNbUsers());
    }
    public function onListAllUsers2(ListAllUsersEvent $event){
        $this->logger->debug("Le second Listener avec le nbre :". $event->getNbUsers());
    }

    public function logKernelRequest(KernelEvent $event){
        dd($event->getRequest());
    }
}
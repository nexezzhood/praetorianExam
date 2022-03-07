<?php

namespace App\Services;

use App\Entity\Request;
use App\Entity\Response;
use App\Event\LogsEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class LogsService
{
    private $eventDispatcher;
    private $doctrine;

    public function __construct(EventDispatcherInterface $eventDispatcher, ManagerRegistry $doctrine)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->doctrine = $doctrine;
    }

    public function log(array $data): array
    {
        if ($data['method'] === 'request') {
            if ($data['operation_type'] === 'update') {
                $entity = $this->doctrine->getRepository(Request::class)->findOneBy(
                    ['ip_address' => $data['entity']['ip_address']]
                );
            } else {
                $entity = new Request();
            }
        } else {
            if ($data['operation_type'] === 'update') {
                $entity = $this->doctrine->getRepository(Response::class)->findOneBy(
                    ['ip_address' => $data['entity']['ip_address']]
                );
            } else {
                $entity = new Response();
            }
        }

        $entityManager = $this->doctrine->getManager();

        $entity->setIpAddress($data['entity']['ip_address']);
        $entity->setLastUpdate($data['entity']['last_update']);
        $entity->setCachedOperations($data['entity']['cached_operations']);
        $entityManager->persist($entity);

        $entityManager->flush();

        $event = new LogsEvent($data);
        $this->eventDispatcher->dispatch($event, LogsEvent::NAME);

        return $data;
    }
}
<?php

namespace App\Services;

use App\Entity\Request;
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
        if ($data['operation_type'] === 'update') {
            $request = $this->doctrine->getRepository(Request::class)->findOneBy(
                ['ip_address' => $data['entity']['ip_address']]
            );
        } else {
            $request = new Request();
        }

        $entityManager = $this->doctrine->getManager();

        $request->setIpAddress($data['entity']['ip_address']);
        $request->setLastUpdate($data['entity']['last_update']);
        $request->setCachedOperations($data['entity']['cached_operations']);
        $entityManager->persist($request);

        $entityManager->flush();

        $event = new LogsEvent($data);
        $this->eventDispatcher->dispatch($event, LogsEvent::NAME);

        return $data;
    }
}
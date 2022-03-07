<?php

namespace App\Event;

use App\Entity\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\EventDispatcher\Event;

class LogsEvent extends Event
{
    public const NAME = 'log';

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function logData(): array {
        return $this->data;
    }

}
<?php namespace Api\Parts\Repositories\EventStore;

use Broadway\Domain\DomainEventStream;
use Broadway\Repository\Repository;

interface PartRepository extends Repository
{
    /**
     * @param $id
     * @param DomainEventStream $eventStream
     * @return mixed
     */
    public function append($id, DomainEventStream $eventStream);

    /**
     * @return array
     */
    public function getStreamIds();
}

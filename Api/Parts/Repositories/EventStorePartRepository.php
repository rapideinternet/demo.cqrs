<?php namespace Api\Parts\Repositories;


use Broadway\Domain\DomainEventStream;
use Broadway\Repository\Repository;

interface EventStorePartRepository extends Repository
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

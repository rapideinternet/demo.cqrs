<?php namespace Api\Parts\Repositories\EventStore\Mysql;

use Api\Parts\Entities\Part;
use Broadway\Domain\DomainEventStream;
use Broadway\EventHandling\EventBus;
use Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStore;
use Doctrine\DBAL\Connection;

class PartRepository extends EventSourcingRepository implements \Api\Parts\Repositories\EventStore\PartRepository
{
    /**
     * @var EventStore
     */
    protected $eventStore;
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(
        EventStore $eventStore,
        EventBus $eventBus,
        Connection $connection,
        array $eventStreamDecorators = []
    )
    {
        $this->eventStore = $eventStore;
        $this->connection = $connection;

        parent::__construct(
            $eventStore,
            $eventBus,
            Part::class,
            new PublicConstructorAggregateFactory(),
            $eventStreamDecorators
        );
    }

    public function append($id, DomainEventStream $eventStream)
    {
        $this->eventStore->append($id, $eventStream);
    }

    /**
     * {@inheritDoc}
     */
    public function getStreamIds()
    {
        $statement = $this->connection->prepare(
            'SELECT DISTINCT uuid FROM ' . config('broadway.event-store.table')
        );
        
        $statement->execute();

        return array_map(
            function ($row) {
                return $row['uuid'];
            },
            $statement->fetchAll()
        );
    }
}

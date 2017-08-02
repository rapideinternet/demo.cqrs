<?php namespace Api\Parts\Console;

use Api\Parts\Repositories\EventStore\PartRepository;
use Broadway\Domain\DateTime;
use Broadway\Domain\DomainEventStream;
use Broadway\EventHandling\EventBus;
use Illuminate\Console\Command;

class ReplayPartsCommand extends Command
{
    /**
     * Date until you want to rebuild the parts
     * Edit this property
     * @var string
     */
    protected $limitDate = '2018-01-30 20:00:00';
    /**
     * The console command name.
     * @var string
     */
    protected $name = 'events:parts';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Rebuild the parts until a specific date. Commands needs to be edited.';
    /**
     * @var PartRepository
     */
    private $eventStore;
    private $eventBuffer = [];
    private $maxBufferSize = 20;
    /**
     * @var EventBus
     */
    private $eventBus;

    public function __construct(PartRepository $eventStore, EventBus $eventBus)
    {
        parent::__construct();

        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $this->comment('Rebuilding stuff...');

        $streamIds = $this->eventStore->getStreamIds();
        $this->process($streamIds);

        $this->comment('Finished rebuilding.');
    }

    private function process($streamIds)
    {
        foreach ($streamIds as $id) {
            $this->comment('Rebuilding: ' . $id);
            $this->rebuildStream($id);
            $this->publishEvents();
        }
    }

    private function rebuildStream($id)
    {
        /** @var \Broadway\EventStore\EventStore $eventStore */
        $eventStore = app(\Broadway\EventStore\EventStore::class);
        $stream = $eventStore->load($id);

        foreach ($stream->getIterator() as $event) {
            $limit = DateTime::fromString($this->limitDate);
            $recordedOnDate = $event->getRecordedOn();
            if ($recordedOnDate->comesAfter($limit)) {
                continue;
            }
            $this->addEventToBuffer($event);
            $this->guardBufferNotFull();
        }
    }

    private function publishEvents()
    {
        $this->eventBus->publish(new DomainEventStream($this->eventBuffer));
        $this->clearEventBuffer();
    }

    private function addEventToBuffer($event)
    {
        $this->eventBuffer[] = $event;
    }

    private function bufferLimitReached()
    {
        return count($this->eventBuffer) > $this->maxBufferSize;
    }

    private function clearEventBuffer()
    {
        $this->eventBuffer = [];
    }

    private function guardBufferNotFull()
    {
        if ($this->bufferLimitReached()) {
            $this->publishEvents();
        }
    }
}

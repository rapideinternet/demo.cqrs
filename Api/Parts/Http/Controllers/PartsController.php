<?php namespace Api\Parts\Http\Controllers;

use Api\Parts\Commands\ManufacturePartCommand;
use Api\Parts\Commands\RemovePartCommand;
use Api\Parts\Commands\RenameManufacturerForPartCommand;
use Api\Parts\Entities\ManufacturerId;
use Api\Parts\Entities\PartId;
use Api\Parts\Repositories\ReadModel\PartRepository as ReadModelPartRepository;
use Broadway\CommandHandling\CommandBus;
use Broadway\EventHandling\SimpleEventBus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;

class PartsController extends Controller
{
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var ReadModelPartRepository
     */
    private $readModelPartRepository;

    /**
     * Injecting the command bus to send commands
     * And a read model to list all manufactured parts
     * @param CommandBus $commandBus
     * @param ReadModelPartRepository $readModelPartRepository
     */
    public function __construct(
        CommandBus $commandBus,
        ReadModelPartRepository $readModelPartRepository
    )
    {
        $this->commandBus = $commandBus;
        $this->readModelPartRepository = $readModelPartRepository;
    }

    /**
     * Display a listing of all the manufactured parts.
     * This reads everything from the read model
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $parts = $this->readModelPartRepository->findAll();

        return view('parts::index', compact('parts'));
    }

    /**
     * Create a Part in the event store as well as in the read model
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $partId = PartId::generate();
        $manufacturerId = ManufacturerId::generate();

        $command = new ManufacturePartCommand($partId, $manufacturerId, $request->get('manufacturer-name'));
        $this->commandBus->dispatch($command);

        return Redirect::route('parts.index')->with('success', 'Part successfully created.');
    }

    /**
     * Update a part aggregate
     * @param Request $request
     * @return array
     */
    public function update(Request $request)
    {
        $partId = PartId::fromString($request->get('pk'));

        $command = new RenameManufacturerForPartCommand($partId, $request->get('value'));
        $this->commandBus->dispatch($command);

        return ['updated' => true];
    }

    public function destroy(Request $request)
    {
        $partId = PartId::fromString($request->get('partId'));

        $command = new RemovePartCommand($partId);
        $this->commandBus->dispatch($command);

        return Redirect::route('parts.index')->with('success', 'Part successfully deleted.');
    }

    public function replay(){
        $eventBus = new SimpleEventBus();

        $events = [];
        foreach ($connection->fetchAll('SELECT * FROM events') as $event) {
            $events[] = new Broadway\Domain\DomainMessage(
                $event['uuid'],
                $event['playhead'],
                $metadataSerializer->deserialize(json_decode($event['metadata'], true)),
                $payloadSerializer->deserialize(json_decode($event['payload'], true)),
                Broadway\Domain\DateTime::fromString($event['recorded_on'])
            );
        }
    }
}

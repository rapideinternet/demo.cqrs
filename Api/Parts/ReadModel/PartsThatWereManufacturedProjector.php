<?php namespace Api\Parts\ReadModel;

use Api\Parts\Events\PartManufacturerWasRenamedEvent;
use Api\Parts\Events\PartWasManufacturedEvent;
use Api\Parts\Events\PartWasRemovedEvent;
use Api\Parts\Repositories\ReadModelPartRepository;
use Assert\Assertion;
use Broadway\ReadModel\Projector;
use Broadway\Repository\Repository;

class PartsThatWereManufacturedProjector extends Projector
{
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(ReadModelPartRepository $repository)
    {
        $this->repository = $repository;
    }

    public function applyPartWasManufacturedEvent(PartWasManufacturedEvent $event)
    {
        try {
            $readModel = $this->getReadModel($event->partId);
        } catch (\Exception $e) {
            $readModel = new PartsThatWereManufactured($event->partId->toString(), $event->manufacturerName);
        }

        $this->repository->save($readModel);
    }

    public function applyPartManufacturerWasRenamedEvent(PartManufacturerWasRenamedEvent $event)
    {
        $readModel = $this->getReadModel($event->partId);

        $readModel->renameManufacturer($event->manufacturerName);

        $this->repository->save($readModel);
    }

    public function applyPartWasRemovedEvent(PartWasRemovedEvent $event)
    {
        $this->repository->remove($event->partId);
    }

    /**
     * @param $partId
     * @return \Api\Parts\ReadModel\PartsThatWereManufactured
     */
    public function getReadModel($partId)
    {
        $partId = (string)$partId;
        $readModel = $this->repository->find($partId);

        Assertion::isInstanceOf($readModel, PartsThatWereManufactured::class);

        return $readModel;
    }
}

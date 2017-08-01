<?php namespace Api\Parts\Commands\Handlers;

use Api\Parts\Commands\ManufacturePartCommand;
use Api\Parts\Commands\RemovePartCommand;
use Api\Parts\Commands\RenameManufacturerForPartCommand;
use Api\Parts\Entities\Part;
use Api\Parts\Repositories\MysqlEventStorePartRepository;
use Broadway\CommandHandling\SimpleCommandHandler;

class PartCommandHandler extends SimpleCommandHandler
{
    /**
     * @var MysqlEventStorePartRepository
     */
    private $repository;

    public function __construct(MysqlEventStorePartRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * A new part aggregate root is created and added to the repository.
     *
     * @param ManufacturePartCommand $command
     */
    protected function handleManufacturePartCommand(ManufacturePartCommand $command)
    {
        $part = Part::manufacture($command->partId, $command->manufacturerId, $command->manufacturerName);
        $this->repository->save($part);
        }

    /**
     * An existing part aggregate root is loaded and renameManufacturerTo() is
     * called.
     *
     * @param RenameManufacturerForPartCommand $command
     */
    protected function handleRenameManufacturerForPartCommand(RenameManufacturerForPartCommand $command)
    {
        $part = $this->repository->load($command->partId);
        $part->renameManufacturer($command->manufacturerName);
        $this->repository->save($part);
    }

    protected function handleRemovePartCommand(RemovePartCommand $command)
    {
        $part = $this->repository->load($command->partId);
        $part->remove();
        $this->repository->save($part);
    }
}

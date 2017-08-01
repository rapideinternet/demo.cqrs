<?php namespace Api\Parts\Commands;

use Api\Core\Domain\Identifier;

class RenameManufacturerForPartCommand
{
    public $partId;
    public $manufacturerName;

    public function __construct(Identifier $partId, $manufacturerName)
    {
        $this->partId = $partId;
        $this->manufacturerName = $manufacturerName;
    }
}

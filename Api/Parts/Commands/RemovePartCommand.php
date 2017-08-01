<?php namespace Api\Parts\Commands;

use Api\Core\Domain\Identifier;

class RemovePartCommand
{
    public $partId;

    public function __construct(Identifier $partId)
    {
        $this->partId = $partId;
    }
}

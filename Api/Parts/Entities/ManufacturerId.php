<?php namespace Api\Parts\Entities;

use Api\Core\Domain\Identifier;
use Api\Core\Domain\UuidIdentifier;
use Ramsey\Uuid\Uuid;

class ManufacturerId extends UuidIdentifier implements Identifier
{
    /**
     * @var Uuid
     */
    protected $value;

    /**
     * Create a new Identifier
     *
     * @param Uuid $value
     */
    public function __construct(Uuid $value)
    {
        $this->value = $value;
    }
}

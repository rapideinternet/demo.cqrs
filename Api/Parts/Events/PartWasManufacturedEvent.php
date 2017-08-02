<?php namespace Api\Parts\Events;

use Api\Parts\Entities\ManufacturerId;
use Api\Parts\Entities\PartId;
use Broadway\Serializer\Serializable;

class PartWasManufacturedEvent implements Serializable
{
    public $partId;
    public $manufacturerId;
    public $manufacturerName;

    public function __construct(PartId $partId, ManufacturerId $manufacturerId, $manufacturerName)
    {
        $this->partId = $partId;
        $this->manufacturerId = $manufacturerId;
        $this->manufacturerName = $manufacturerName;
    }

    /**
     * @param  array $data
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $partId = PartId::fromString($data['partId']);

        return new self($partId, $data['manufacturerId'], $data['manufacturerName']);
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'partId' => $this->partId->toString(),
            'manufacturerId' => $this->manufacturerId->toString(),
            'manufacturerName' => $this->manufacturerName,
        ];
    }
}

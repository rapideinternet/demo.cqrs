<?php namespace Api\Parts\Events;

use Api\Parts\Entities\PartId;
use Broadway\Serializer\Serializable;

class PartManufacturerWasRenamedEvent implements Serializable
{
    public $partId;
    public $manufacturerName;

    public function __construct(PartId $partId, $manufacturerName)
    {
        $this->partId = $partId;
        $this->manufacturerName = $manufacturerName;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        $partId = PartId::fromString($data['partId']);

        return new self($partId, $data['manufacturerName']);
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'partId' => $this->partId->toString(),
            'manufacturerName' => $this->manufacturerName,
        ];
    }
}

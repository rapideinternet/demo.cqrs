<?php namespace Api\Parts\ReadModel;

use Broadway\ReadModel\SerializableReadModel;

class PartsThatWereManufactured implements SerializableReadModel
{
    /**
     * @var int
     */
    public $manufacturedPartId;
    /**
     * @var string
     */
    public $manufacturerName;

    public function __construct($manufacturedPartId, $manufacturerName)
    {
        $this->manufacturedPartId = $manufacturedPartId;
        $this->manufacturerName = $manufacturerName;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->manufacturedPartId;
    }

    public function renameManufacturer($manufacturerName)
    {
        $this->manufacturerName = $manufacturerName;
    }

    /**
     * @param  array $data
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self($data['manufacturedPartId'], $data['manufacturerName']);
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'manufacturedPartId' => $this->manufacturedPartId,
            'manufacturerName' => $this->manufacturerName
        ];
    }
}

<?php namespace Api\Parts\Repositories\ReadModel\Elasticsearch;

use Api\Parts\ReadModel\PartsThatWereManufactured;
use Broadway\ReadModel\ElasticSearch\ElasticSearchRepository;
use Broadway\Serializer\Serializer;
use Elasticsearch\Client;

class PartRepository extends ElasticSearchRepository implements \Api\Parts\Repositories\ReadModel\PartRepository
{
    public function __construct(
        Client $client,
        Serializer $serializer,
        array $notAnalyzedFields = []
    )
    {
        parent::__construct($client, $serializer, 'parts', PartsThatWereManufactured::class, $notAnalyzedFields);
    }
}

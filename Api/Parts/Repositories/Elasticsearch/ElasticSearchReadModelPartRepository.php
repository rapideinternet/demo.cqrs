<?php namespace Api\Parts\Repositories\Elasticsearch;

use Api\Parts\ReadModel\PartsThatWereManufactured;
use Api\Parts\Repositories\ReadModelPartRepository;
use Broadway\ReadModel\ElasticSearch\ElasticSearchRepository;
use Broadway\Serializer\Serializer;
use Elasticsearch\Client;

class ElasticSearchReadModelPartRepository extends ElasticSearchRepository implements ReadModelPartRepository
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

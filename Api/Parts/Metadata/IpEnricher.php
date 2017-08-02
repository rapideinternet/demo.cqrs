<?php namespace Api\Parts\Metadata;

use Broadway\Domain\Metadata;
use Broadway\EventSourcing\MetadataEnrichment\MetadataEnricher;
use Illuminate\Support\Facades\Request;

class IpEnricher implements MetadataEnricher
{
    /**
     * The constructor
     */
    public function __construct()
    {
    }

    /**
     * @param Metadata $metadata
     * @return Metadata
     */
    public function enrich(Metadata $metadata)
    {
        $ip = Request::ip();

        return $metadata->merge(Metadata::kv('ip', $ip));
    }
}
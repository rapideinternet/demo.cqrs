<?php namespace Api\Parts\Processors;

use Api\Parts\Events\PartManufacturerWasRenamedEvent;
use Broadway\Processor\Processor;
use Illuminate\Support\Facades\Log;

class LogEntryProcessor extends Processor
{

    protected function handlePartManufacturerWasRenamedEvent(PartManufacturerWasRenamedEvent $event)
    {
        Log::info('Part was renamed ' . $event->partId);
    }

}
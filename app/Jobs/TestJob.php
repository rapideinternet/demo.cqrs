<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Output\ConsoleOutput;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use InteractsWithConsole;
    /**
     * @var int
     */
    private $sleepTimer;
    /**
     * @var
     */
    private $id;

    /**
     * Create a new job instance.
     *
     * @param $id
     * @param int $sleepTimer
     */
    public function __construct($id, $sleepTimer = 5)
    {
        //
        $this->sleepTimer = $sleepTimer;
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ConsoleOutput $consoleOutput)
    {
        $consoleOutput->writeln('Started job ' . $this->id);
        usleep($this->sleepTimer);
        $consoleOutput->writeln('Finished ' . $this->id);
    }
}

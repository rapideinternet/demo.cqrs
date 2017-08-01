<?php

namespace App\Console\Commands;

use App\Jobs\TestJob;
use Illuminate\Console\Command;

class RabbitmqProduce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:product {amount=5} {sleep=12}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Put messages on the queue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $queue = 'test_queue_package';

        for ($i = 0; $i < $this->argument('amount'); $i++) {
            $job = (new TestJob($i, $this->argument('sleep')))->onQueue($queue);
            dispatch($job);
            $this->output->writeln('Dispatched job ' . $i);
        }
    }
}

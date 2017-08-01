<?php

namespace App\Console\Commands;

use App\Events\UserCreated;
use Illuminate\Console\Command;

class TestCreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'event:usercreated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the user created event';

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
        for ($i = 0; $i < 5; $i++) {
            $user = new \App\Models\User(['name' => $i, 'email' => rand(), 'password' => '']);
            $user->save();

            event(new UserCreated($user));
        }
    }
}

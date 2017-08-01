<?php

namespace App\Listeners;

use App\Events\UserCreated;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Symfony\Component\Console\Output\ConsoleOutput;

class SendUserWelcomeMailSubscriber extends TransactionalSubscriber
{
    use InteractsWithConsole;
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * Create the event listener.
     *
     * @param Mailer $mailer
     * @param ConsoleOutput $consoleOutput
     */
    public function __construct(Mailer $mailer, ConsoleOutput $consoleOutput)
    {
        parent::__construct();
        $this->mailer = $mailer;
        $this->consoleOutput = $consoleOutput;
        $this->enabled = true;

    }

    /**
     * Handle the event.
     *
     * @param  UserCreated $event
     * @return void
     */
    public function handleEvent(UserCreated $event)
    {
        $user = $event->getUser();

        $user->name = 'Henk';
        $user->save();

        $user->email = '';
        $user->save();

        \Log::info('Job success');
    }

    public function failed(UserCreated $event, $exception)
    {
        \Log::info('Job failed ' . $exception->getMessage());

    }
}

<?php

namespace App\Listeners;


use App\Models\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager as DB;

abstract class TransactionalSubscriber implements ShouldQueue
{
    /**
     * @var DB $db
     */
    protected $db;
    /**
     * @var \Illuminate\Foundation\Application $app
     */
    protected $app;
    /**
     * @var boolean
     */
    protected $enabled = true;

    /**
     * Create a new event instance.
     *
     * @param User $user
     */
    protected function __construct()
    {
        $this->app = app();
        $this->db = $this->app->make(DB::class);
    }

    /**
     * @param array ...$args
     * @return void
     */
    public final function handle(...$args): void
    {
        $this->startHandling();
        try {
            call_user_func_array([$this, 'handleEvent'], $args);
            $this->eventSuccess();
        } catch (Exception $exception) {
            $this->eventFailed($exception);
        }
    }

    protected function startHandling()
    {
        $this->callBeforeTransaction();
        if ($this->enabled) {
            $this->db->beginTransaction();
        }
    }

    /**
     *
     */
    protected function eventSuccess()
    {
        if ($this->enabled) {
            $this->db->commit();
        }
    }

    /**
     * @param Exception $exception
     * @throws Exception
     */
    protected function eventFailed(Exception $exception)
    {
        if ($this->enabled) {
            $this->db->rollBack();
        }
        throw $exception;
    }

    /**
     * Function called before transaction
     */
    protected function callBeforeTransaction()
    {
        if (method_exists($this, 'beforeTransaction')) {
            $this->app->call([$this, 'beforeTransaction']);
        }
    }
}
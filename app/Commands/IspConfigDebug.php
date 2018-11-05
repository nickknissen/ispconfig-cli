<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class IspconfigDebug extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ispconfig:debug
                            {--filter= : filter available remote functions}
                            {--call= : remote function ex. server_get_php_versions}
                            {--arg=* }';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Debug command to list available remote functions to the current user';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(\App\ISPConfig $ispconfig)
    {
        $session = $ispconfig->login();

        if ($this->option('call')) {

            $args = collect($this->option('arg'))->mapWithKeys(function ($argument) {
                list($key, $value) = explode(':', $argument);
                return [$key => $value];
            })->merge(['session_id' => $session])->toArray();

            $response = $ispconfig->request($this->option('call'), $args);

            dd($response->json());
            return;
        }

        $functions = $ispconfig->getAvailableFunctions();

        if ($this->option('filter')) {
            $functions = $functions->filter(function ($func) {
                return str_contains($func, $this->option('filter'));
            });
        }
        $functions = $functions->map(function ($item) {
            return ['remote function' => $item] ;
        });

        $this->table(
            ['Remote functions'],
            $functions->toArray()
        );
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}

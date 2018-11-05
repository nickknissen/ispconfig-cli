<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class IspconfigLogin extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'ispconfig:login';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(\App\ISPConfig $ispconfig)
    {
        $session = $ispconfig->login();

        $this->info('Logged in successfuly');
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

<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ShellUsersCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'shell-users';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List shell users';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(\App\ISPConfig $ispconfig)
    {
        $session = $ispconfig->login();

        $users = $ispconfig->getShellUsers();

        $keys = ['username', 'active', 'puser', 'pgroup', 'server_id'];

        $users = $users->map(function ($item) use ($keys) {
            return array_merge(array_flip($keys), array_only($item, $keys));
        })->toArray();

        $tableHeaders = collect($keys)->map(function ($key) {
            return ucfirst($key);
        });

        $this->table($tableHeaders, $users);
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

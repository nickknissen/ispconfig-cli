<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ServersCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'servers';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List servers';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(\App\ISPConfig $ispconfig)
    {
        $session = $ispconfig->login();

        $servers = $ispconfig->getServers($session);

        $services = $servers->map(function ($server) use ($ispconfig) {
            return $ispconfig->getServersServices($server['server_id']);
        });

        $this->table(
            ['Id', 'Name', 'Mail enabled', 'Web enabled', 'DNS enabled', 'File enabled', 'Database enabled', 'Virtualization enabled', 'Proxy enabled', 'Firewall enabled', 'Mirror server enabled'],
            $servers->zip($services)->map(function ($item) {
                return array_merge($item[0], $item[1]->toArray());
            })
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

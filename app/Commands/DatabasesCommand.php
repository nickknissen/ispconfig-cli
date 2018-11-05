<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class DatabasesCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'databases';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List databases';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(\App\ISPConfig $ispconfig)
    {
        $session = $ispconfig->login();

        $keys = ['database_id', 'database_name', 'database_user', 'database_password'];

        $clients = $ispconfig->getClients();

        $bar = $this->output->createProgressBar(count($clients));
        $bar->setFormat("%message% %clientid% \n %current%/%max% [%bar%] %percent:3s%% \n");

        $databases = $clients->reduce(function ($carry, $clientId) use ($keys, $ispconfig, $bar) {
            $bar->setMessage("Fetching database data for client");
            $bar->setMessage($clientId, 'clientid');

            $databases = $ispconfig->getDatabases($clientId)->map(function ($db) use ($keys) {
                return array_only($db, $keys);
            });

            $bar->advance();
            return $carry->merge($databases);
        }, collect());

        $bar->clear();

        $tableHeaders = collect($keys)->map(function ($key) {
            return ucfirst(str_replace('database_', '', $key));
        });

        $this->table($tableHeaders, $databases);
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

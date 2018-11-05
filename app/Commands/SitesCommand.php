<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class SitesCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites {--id=*}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List sites';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(\App\ISPConfig $ispconfig)
    {
        $session = $ispconfig->login();

        $keys = ['client_id', 'sys_userid', 'sys_groupid', 'customer_no', 'company_name', 'added_by'];

        $ids = $this->option('id') ? collect($this->option('id')) : $ispconfig->getClients();

        $bar = $this->output->createProgressBar(count($ids));
        $bar->setFormat("%message% Client %clientid% \n %current%/%max% [%bar%] %percent:3s%% \n");

        $clients = $ids->map(function ($clientId) use ($ispconfig, $bar, $keys) {
            $bar->setMessage("Fetching client data...");
            $bar->setMessage($clientId, 'clientid');
            $client = $ispconfig
                ->getClients($clientId)
                ->only($keys);
            $bar->advance();
            return $client;
        });

        $tableHeaders = collect($keys)->map(function ($key) {
            return ucfirst(str_replace('_', ' ', $key));
        });

        $bar->clear();

        $this->table($tableHeaders, $clients);
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

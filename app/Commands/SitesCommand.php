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
    protected $signature = 'sites {--client-id=*}';

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

        $keys = ['domain', 'domain_id', 'document_root', 'active', 'client_id'];

        $ids = $this->option('client-id') ? collect($this->option('client-id')) : $ispconfig->getClients();

        $bar = $this->output->createProgressBar(count($ids));
        $bar->setFormat("%message% %clientid% \n %current%/%max% [%bar%] %percent:3s%% \n");

        $sites = $ids->reduce(function ($carry, $clientId) use ($keys, $ispconfig, $bar) {
            $bar->setMessage("Fetching site data for client");
            $bar->setMessage($clientId, 'clientid');
            $sysGroupId = $clientId + 1;

            $sites = $ispconfig->getSitesByUser(null, $sysGroupId)->map(function ($db) use ($keys, $clientId) {
                $db['client_id'] = $clientId;
                return array_only($db, $keys);
            });

            $bar->advance();
            return $carry->merge($sites);
        }, collect())
            ->map(function ($site) {
                $site["active"] = $site["active"] == "y" ? "✔️" : "❌";
                return $site;
            });

        $bar->clear();

        $this->table($keys, $sites->sortBy('domain'));
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

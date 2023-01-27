<?php

namespace Laravel\Sail\Console;

use Illuminate\Console\Command;
use Laravel\Sail\Console\Concerns\InteractsWithDockerComposeServices;

class AddCommand extends Command
{
    use InteractsWithDockerComposeServices;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sail:add
        {services? : The services that should be added to your setup.}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Laravel Sail\'s services to an existing sail install';

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        if ($this->argument('services')) {
            $services = $this->argument('services') == 'none' ? [] : explode(',', $this->argument('services'));
        } elseif ($this->option('no-interaction')) {
            $services = $this->defaultServices;
        } else {
            $services = $this->gatherServicesWithSymfonyMenu();
        }

        if ($invalidServices = array_diff($services, $this->services)) {
            $this->error('Invalid services ['.implode(',', $invalidServices).'].');

            return 1;
        }

        $this->buildDockerCompose($services);
        $this->replaceEnvVariables($services);
        $this->configurePhpUnit();

        $this->info('Additional Sail services installed successfully.');

        $this->prepareInstallation($services);
    }
}

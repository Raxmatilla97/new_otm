<?php

namespace Modular\Modular\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeRouteCommand extends Command
{
    protected $signature = 'modular:make-route {moduleName} {resourceName}';

    protected $description = 'Create a new route for a Module.';

    protected string $moduleName;

    protected string $resourceName;

    /**
     * Execute the console command.
     *
     * @return int|null
     */
    public function handle()
    {
        $this->moduleName = Str::studly($this->argument('moduleName'));
        $this->resourceName = Str::studly($this->argument('resourceName'));

        if (! $this->moduleExists()) {
            return self::FAILURE;
        }

        $this->comment('Module '.$this->moduleName.' found, creating route...');
        $this->createModuleRoute();

        return self::SUCCESS;
    }

    private function moduleExists(): bool
    {
        if (! is_dir(base_path("modules/{$this->moduleName}"))) {
            $this->error("Module {$this->moduleName} does not exist.");

            return false;
        }

        return true;
    }

    private function createModuleRoute(): void
    {
        $stub = file_get_contents(__DIR__.'/../../stubs/module-stub/modules/routes/app.stub');

        $stub = str_replace('{{ ModuleName }}', $this->moduleName, $stub);
        $stub = str_replace('{{ ResourceName }}', $this->resourceName, $stub);
        $stub = str_replace('{{ resourceName }}', Str::camel($this->resourceName), $stub);

        $path = base_path("modules/{$this->moduleName}/routes/app.php");

        if (! file_exists($path)) {
            file_put_contents($path, $stub);
        } else {
            file_put_contents($path, $stub, FILE_APPEND);
        }
    }
}
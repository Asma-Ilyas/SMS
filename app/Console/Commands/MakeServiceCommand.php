<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Services/{$name}.php");

        if (File::exists($path)) {
            $this->error("Service {$name} already exists!");
            return 1;
        }

        // Ensure directory exists
        File::ensureDirectoryExists(app_path('Services'));

        // Stub content
        $stub = "<?php\n\nnamespace App\Services;\n\nclass {$name}\n{\n    //\n}\n";

        File::put($path, $stub);

        $this->info("Service created successfully: app/Services/{$name}.php");
        return 0;
    }
}
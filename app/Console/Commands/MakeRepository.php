<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    protected $signature = 'make:repository {name}';
    protected $description = 'Create a repository and interface';

    public function handle()
    {
        $name = $this->argument('name');

        $interfacePath = app_path("Repositories/{$name}RepositoryInterface.php");
        $repositoryPath = app_path("Repositories/{$name}Repository.php");

        // Create Repositories folder if it doesn't exist
        if (!File::exists(app_path('Repositories'))) {
            File::makeDirectory(app_path('Repositories'));
        }

        // Create Interface
        if (!File::exists($interfacePath)) {
            File::put($interfacePath, $this->getInterfaceContent($name));
            $this->info("Created Interface: {$name}RepositoryInterface.php");
        } else {
            $this->error("Interface already exists!");
        }

        // Create Repository
        if (!File::exists($repositoryPath)) {
            File::put($repositoryPath, $this->getRepositoryContent($name));
            $this->info("Created Repository: {$name}Repository.php");
        } else {
            $this->error("Repository already exists!");
        }
    }

    private function getInterfaceContent($name)
    {
        return "<?php

namespace App\Repositories;

interface {$name}RepositoryInterface
{
    public function getAll();
    public function findById(\$id);
    public function create(array \$data);
    public function update(\$id, array \$data);
    public function delete(\$id);
}
";
    }

    private function getRepositoryContent($name)
    {
        return "<?php

namespace App\Repositories;

use App\Models\\{$name};

class {$name}Repository implements {$name}RepositoryInterface
{
    public function getAll()
    {
        return {$name}::all();
    }

    public function findById(\$id)
    {
        return {$name}::find(\$id);
    }

    public function create(array \$data)
    {
        return {$name}::create(\$data);
    }

    public function update(\$id, array \$data)
    {
        \$model = {$name}::find(\$id);
        if (\$model) {
            \$model->update(\$data);
            return \$model;
        }
        return null;
    }

    public function delete(\$id)
    {
        \$model = {$name}::find(\$id);
        if (\$model) {
            return \$model->delete();
        }
        return false;
    }
}
";
    }
}

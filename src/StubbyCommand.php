<?php


namespace Grosv\Stubby;


use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use TitasGailius\Terminal\Terminal;
use function PHPUnit\Framework\directoryExists;

class StubbyCommand extends Command
{
    protected $signature = 'new {thing} {name?}';

    private Collection $files;
    private ?string $rawName;
    private ?string $thing;
    private string $studlyName;

    public function __construct()
    {
        parent::__construct();
        $this->files = collect([]);
    }

    public function handle(): int
    {
        $this->thing = $this->argument('thing');

        if ($this->thing === 'stubs') {
            Terminal::run('php artisan stub:publish');
            Terminal::run('php artisan vendor:publish --provider="Grosv\Stubby\StubbyProvider" --tag="stubs"  --force');
            exit(0);
        }
        $this->rawName = $this->argument('name');
        $this->studlyName = Str::studly($this->argument('name'));

        if (empty($this->thing)) {
            $this->error('What are you trying to build?');

            return 1;
        }
        if (empty($this->rawName)) {
            $this->error('What are you trying to name the thing you are trying to build?');
            return 1;
        }
        switch ($this->thing) {
            case 'ide':
                File::append(base_path('.env'), "\nSTUBBY_FILE_OPEN_COMMAND=".$this->rawName);
                $this->files->push(base_path('.env'));
                break;

            case 'action':
                if (!File::isDirectory(dirname(app_path('Actions/'.$this->studlyName)))) {
                    File::makeDirectory(dirname(app_path('Actions/'.$this->studlyName)), 0777, $recursive = true, $force = true);
                }
                $stub = File::get(base_path('stubs/action.stub'));
                File::put(app_path('Actions/'.$this->studlyName).'.php', str_replace('{{ class }}', $this->studlyName, $stub));
                $this->files->push(app_path('/Actions/'.$this->studlyName.'.php'));
                $this->info('Action created successfully.');
                $this->call('make:test', ['name' => 'Actions/'.$this->studlyName.'Test']);
                $this->files->push('tests/Feature/Actions/'.$this->studlyName.'Test.php');
                break;
            case 'command':
                $this->call('make:command', ['name' => $this->studlyName]);
                $this->files->push(app_path('Console/Commands/'.$this->studlyName.'.php'));
                $this->call('make:test', ['name' => 'Commands/'.$this->studlyName.'Test']);
                $this->files->push('tests/Feature/Commands/'.$this->studlyName.'Test.php');
                break;
            case 'controller':
                if (Str::endsWith($this->studlyName, 'Controller')) {
                    $this->call('make:controller', ['name' => $this->studlyName, '--resource' => true, '--model' => str_replace('Controller', '', $this->studlyName)]);
                    $this->files->push(base_path('routes/api.php'));
                } else {
                    $this->call('make:controller', ['name' => $this->studlyName]);
                    $this->files->push(base_path('routes/web.php'));
                }
                $this->files->push(app_path('Http/Controllers/'.$this->studlyName.'.php'));

                $this->call('make:test', ['name' => 'Http/Controllers/' . $this->studlyName.'Test']);
                $this->files->push('tests/Feature/Http/Controllers/'.$this->studlyName.'Test.php');
                File::put(resource_path('views/'.Str::snake(Str::replaceLast('Controller', '', $this->studlyName)).'.blade.php'), "@extends('layouts.app')\n@section('content')\n\n@endsection");
                $this->files->push(resource_path('views/'.Str::snake(str_replace('Controller', '', $this->studlyName)).'.blade.php'));
                $this->info('Template created successfully.');

                break;
            case 'livewire':
                $this->call('make:livewire', ['name' => Str::slug($this->rawName)]);
                $this->files->push(app_path('Http/Livewire/'.$this->studlyName.'.php'));
                $this->files->push(resource_path('/views/livewire/'.$this->rawName.'.blade.php'));
                $this->call('make:test', ['name' => 'Http/Livewire/'.$this->studlyName.'Test']);
                $this->files->push(base_path('tests/Feature/Http/Livewire/'.$this->studlyName.'Test.php'));
                break;
            case 'model':
                $this->call('make:model', ['name' => $this->studlyName, '-m' => true, '-f' => true]);
                // This didn't work
                if (file_exists(app_path($this->studlyName.'.php'))) {
                    $this->files->push(app_path($this->studlyName.'.php'));
                }
                else {
                    $this->files->push(app_path('Models/'.$this->studlyName.'.php'));
                }
                foreach (scandir(database_path('migrations/')) as $file) {
                    if (Str::contains($file, 'create_'.Str::snake(Str::plural($this->studlyName)))) {
                        $this->files->push(database_path('migrations/'.$file));
                    }
                }
                $this->files->push(database_path('factories/'.$this->studlyName.'Factory.php'));

                break;

            default:
                $this->call('make'.$this->thing, ['name' => $this->rawName]);
            break;

        }

        $ide = config('stubby.file_open_command');

        if ($ide) {
            $this->files->each(function ($file) use ($ide) {
                Terminal::run("$ide $file");
            });
        }


        return 0;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetEnvVariables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:set {key} {value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set an environment variable in .env file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $key = $this->argument('key');
        $value = $this->argument('value');

        $envFile = './.env';

        $str = file_get_contents($envFile);

        $oldValue = env($key);

        if ($oldValue !== null) {
            file_put_contents($envFile, str_replace(
                "$key=" . $oldValue,
                "$key=" . $value,
                $str
            ));
        } else {
            file_put_contents($envFile, $str . PHP_EOL . "$key=$value");
        }

        $this->info("$key set successfully.");
    }
}

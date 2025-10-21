<?php

namespace App\Console\Commands;

use Database\Seeders\DemoSeeder;
use Illuminate\Console\Command;

class DemoSetupCommand extends Command
{
    protected $signature = 'app:demo';

    protected $description = 'Seed demo data and output login credentials.';

    public function handle(): int
    {
        $this->call('migrate', ['--force' => true]);
        $this->call('db:seed', ['--class' => DemoSeeder::class, '--force' => true]);

        $this->info('Demo data seeded. Login with email demo@kinlink.test and password password-demo.');

        return self::SUCCESS;
    }
}

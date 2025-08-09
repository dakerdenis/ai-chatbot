<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $c = Client::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test Client',
                'password' => bcrypt('secret123'),
                'api_token' => bin2hex(random_bytes(20)),
                'plan' => 'trial',
            ]
        );

        $c->domains()->firstOrCreate(['domain' => '127.0.0.1']);
        $c->domains()->firstOrCreate(['domain' => 'localhost']);

        $this->command->info('Client API token: '.$c->api_token);
    }
}

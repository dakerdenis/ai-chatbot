<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder {
    public function run(): void {
        Admin::firstOrCreate(
            ['email'=>'admin@example.com'],
            ['name'=>'Super Admin','password'=>bcrypt('admin123')]
        );
    }
}

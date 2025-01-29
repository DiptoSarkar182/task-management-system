<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch all user IDs from the database
        $userIds = User::pluck('id')->toArray();

        // Ensure there are users before seeding tasks
        if (empty($userIds)) {
            $this->command->info('No users found. Please seed users first.');
            return;
        }

        // Create 30 tasks
        Task::factory(30)->create([
            'user_id' => fn() => $userIds[array_rand($userIds)], // Assign a random user ID
        ]);
    }
}

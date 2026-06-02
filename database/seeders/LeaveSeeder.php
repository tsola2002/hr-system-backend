<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Leave;

class LeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Leave::insert([
            [
                'user' => 'Admin',
                'leave_type' => 'Unpaid',
                'start_date' => '2026-05-21',
                'end_date' => '2026-05-21',
                'is_half_day' => false,
                'total_days' => '1 Day(s)',
                'status' => 'Approved',
            ],
            [
                'user' => 'Admin',
                'leave_type' => 'Sick',
                'start_date' => '2026-04-10',
                'end_date' => '2026-04-10',
                'is_half_day' => false,
                'total_days' => '1 Day(s)',
                'status' => 'Rejected',
            ],
            [
                'user' => 'Admin',
                'leave_type' => 'Annual',
                'start_date' => '2026-06-15',
                'end_date' => '2026-06-15',
                'is_half_day' => true,
                'total_days' => '0.5 Day(s)',
                'status' => 'Pending',
            ],
        ]);
    }
}

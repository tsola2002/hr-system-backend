<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Get or create an Admin user for migrating old data
            $adminUser = User::where('name', 'Admin')->first();
            if (!$adminUser) {
                $adminUser = User::create([
                    'name' => 'Admin',
                    'email' => 'admin@hrms.local',
                    'password' => bcrypt('password'),
                    'role' => 'manager'
                ]);
            }

            // Add new columns first
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');

            // Migrate data from user (string) column to user_id (FK)
            DB::statement("UPDATE leaves SET user_id = {$adminUser->id} WHERE user = 'Admin' OR user IS NULL");

            // Drop old user column
            $table->dropColumn('user');

            // Add foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['approved_by']);

            // Restore old user column
            $table->string('user')->default('Admin')->after('id');

            // Migrate data back
            DB::statement("UPDATE leaves SET user = (SELECT name FROM users WHERE users.id = leaves.user_id LIMIT 1)");

            // Drop new columns
            $table->dropColumn(['user_id', 'approved_by', 'approved_at', 'rejection_reason']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Drop indexes that depend on type column
            $table->dropIndex('rooms_type_index');
            
            // Add new columns
            $table->text('description')->nullable()->after('name');
            $table->string('slug')->unique()->after('description');
            $table->foreignId('created_by_id')->nullable()->after('is_private')->constrained('users')->cascadeOnDelete();
            
            // Drop the type column
            $table->dropColumn('type');
            
            // Add indexes
            $table->index('slug');
        });
        
        // Rename room_user to room_members if it exists
        if (Schema::hasTable('room_user')) {
            Schema::rename('room_user', 'room_members');
            
            Schema::table('room_members', function (Blueprint $table) {
                $table->enum('role', ['admin', 'moderator', 'member'])->default('member')->after('user_id');
                // joined_at already exists from room_user table
                $table->index(['room_id', 'role']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['created_by_id']);
            $table->dropColumn(['description', 'slug', 'created_by_id']);
            $table->enum('type', ['public', 'private', 'direct'])->default('public')->after('name');
        });
        
        if (Schema::hasTable('room_members')) {
            Schema::table('room_members', function (Blueprint $table) {
                $table->dropColumn(['role']);
            });
            Schema::rename('room_members', 'room_user');
        }
    }
};
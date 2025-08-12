<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['public', 'private', 'direct'])->default('public');
            $table->boolean('is_private')->default(false);
            $table->timestamps();

            $table->index('type');
            $table->index('is_private');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

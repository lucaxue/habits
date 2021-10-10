<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHabitsTable extends Migration
{
    public function up(): void
    {
        Schema::create('habits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('streak');
            $table->json('frequency');
            $table->datetime('last_completed')->nullable();
            $table->datetime('last_incompleted')->nullable();
            $table->boolean('stopped')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
}

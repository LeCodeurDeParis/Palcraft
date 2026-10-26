<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('block_palette', function (Blueprint $table) {
            $table->foreignId('palette_id')->constrained()->cascadeOnDelete();
            $table->foreignId('block_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('position');

            $table->primary(['palette_id', 'position']);
            $table->unique(['palette_id', 'block_id']);
            $table->index('block_id');
        });

        // SQLite cannot add a CHECK constraint after table creation.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE block_palette ADD CONSTRAINT block_palette_position_check CHECK (position BETWEEN 1 AND 5)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_palette');
    }
};

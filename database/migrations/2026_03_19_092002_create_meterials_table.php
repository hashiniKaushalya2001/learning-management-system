<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meterials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('department')
                ->constrained('departments')
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->string('meterial');

            $table->string('aim')->nullable();
            $table->string('lecturer')->nullable();
            $table->string('semester')->nullable();
            $table->string('duration')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meterials');
    }
};

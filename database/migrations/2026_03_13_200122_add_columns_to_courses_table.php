<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->after('id')->nullable();
            $table->string('department')->after('course_id')->nullable();
        });
    }


    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['course_id', 'department']);
        });
    }
};

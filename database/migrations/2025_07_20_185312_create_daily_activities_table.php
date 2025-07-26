<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Nullable for guest users
            $table->date('activity_date');
            $table->integer('questions_attempted')->default(0);
            $table->integer('questions_completed')->default(0);
            $table->integer('points_earned')->default(0);
            $table->timestamps();
            
            // Ensure one record per user per day
            $table->unique(['user_id', 'activity_date']);
            
            // Index for performance
            $table->index(['user_id', 'activity_date']);
            $table->index('activity_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_activities');
    }
}

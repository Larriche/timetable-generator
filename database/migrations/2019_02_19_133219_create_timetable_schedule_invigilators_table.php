<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimetableScheduleInvigilatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timetable_schedule_invigilators', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('timetable_schedule_id')->unsigned();
            $table->integer('invigilator_id')->unsigned();
            $table->timestamps();

            $table->foreign('timetable_schedule_id')
                ->references('id')
                ->on('timetable_schedules')
                ->onDelete('cascade');

            $table->foreign('invigilator_id')
                ->references('id')
                ->on('professors')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timetable_schedule_invigilators');
    }
}

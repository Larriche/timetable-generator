<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttendanceFieldToTimetableSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('timetable_schedules', function (Blueprint $table) {
            $table->integer('attendance')->unsigned()->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timetable_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('timetable_schedules', 'attendance')) {
                $table->dropColumn('attendance');
            }
        });
    }
}

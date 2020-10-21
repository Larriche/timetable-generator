<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSizeFieldToCourseClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('courses_classes', function (Blueprint $table) {
            $table->integer('size')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('courses_classes', function (Blueprint $table) {
            if (Schema::hasColumn('courses_classes', 'size')) {
                $table->dropColumn('size');
            }
        });
    }
}

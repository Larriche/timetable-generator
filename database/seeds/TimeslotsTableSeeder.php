<?php

use Illuminate\Database\Seeder;

class TimeslotsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('timeslots')->insert([
            [
                'time' => '8:30 - 11:30',
                'rank' => 1,
                'label' => 'Morning',
                'type' => 'EXAM'
            ],
            [
                'time' => '12:30 - 15:30',
                'rank' => 2,
                'label' => 'Afternoon',
                'type' => 'EXAM'
            ],
            [
                'time' => '16:30 - 19:30',
                'rank' => 3,
                'label' => 'Evening',
                'type' => 'EXAM'
            ],
            [
                'time' => '8:00 - 10:00',
                'rank' => 1,
                'label' => '8:00 - 10:00',
                'type' => 'NORMAL'
            ],
            [
                'time' => '10:30 - 12:30',
                'rank' => 2,
                'label' => '10:30 - 12:30',
                'type' => 'NORMAL'
            ],
            [
                'time' => '13:00 - 15:00',
                'rank' => 3,
                'label' => '13:00 - 15:00',
                'type' => 'NORMAL'
            ],
            [
                'time' => '15:00 - 17:00',
                'rank' => 4,
                'label' => '15:00 - 17:00',
                'type' => 'NORMAL'
            ]
        ]);
    }
}

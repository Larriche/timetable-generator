<?php

namespace App\Models;

class NormalTimetableSchedule extends TimetableSchedule
{
    /**
     * Table used by this model
     *
     * @var string
     */
    protected $table = 'normal_timetable_schedules';

    /**
     * The timeslot this exam is scheduled for
     *
     * @return App\Models\Timeslot Timeslot
     */
    public function timeslot()
    {
        return $this->belongsTo(Timeslot::class, 'timeslot_id');
    }

    /**
     * Professor taking a class
     *
     * @return App\Models\Professor A professor
     */
    public function professor()
    {
        return $this->belongsTo(Professor::class, 'professor_id');
    }
}

<?php

namespace App\Models;

class ExamTimetableSchedule extends TimetableSchedule
{
    /**
     * Table used by this model
     *
     * @var string
     */
    protected $table = 'exam_timetable_schedules';

    /**
     * Non mass assignable fields
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Custom attributes
     *
     * @var array
     */
    protected $appends = ['class_course'];

    /**
     * The timeslot this exam is scheduled for
     *
     * @return App\Models\Timeslot Timeslot
     */
    public function timeslot()
    {
        return $this->belongsTo(ExamTimeslot::class, 'timeslot_id');
    }

    /**
     * Invigilators for a give exam scheduling
     *
     * @return App\Models\Professor Collection of lecturers
     */
    public function invigilators()
    {
        return $this->belongsToMany(Professor::class, 'timetable_schedule_invigilators', 'timetable_schedule_id', 'invigilator_id');
    }
}

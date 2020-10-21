<?php

namespace App\Models;

class TimetableSchedule extends Model
{
    /**
     * Table used by this model
     *
     * @var string
     */
    protected $table = 'timetable_schedules';

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

    public function timetable()
    {
        return $this->belongsTo(Timetable::class, 'timetable_id');
    }

    /**
     * The course scheduled for this timetable schedule
     *
     * @return App\Models\Course Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * The class that has this timetable schedule
     *
     * @return App\Models\CollegeClass Class
     */
    public function class()
    {
        return $this->belongsTo(CollegeClass::class, 'class_id');
    }

    /**
     * Rooms that a scheduled exams is supposed to take place in
     *
     * @return App\Models\Room Collection of rooms
     */
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'timetable_schedule_rooms', 'timetable_schedule_id', 'room_id');
    }

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
     * Invigilators for a give exam scheduling
     *
     * @return App\Models\Professor Collection of lecturers
     */
    public function invigilators()
    {
        return $this->belongsToMany(Professor::class, 'timetable_schedule_invigilators', 'timetable_schedule_id', 'invigilator_id');
    }

    /**
     * Incidents recorded for a timetable schedule
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function incidents()
    {
        return $this->hasMany(ExamIncident::class, 'schedule_id');
    }

    /**
     * Get the class course for this schedule
     */
    public function getClassCourseAttribute()
    {
        return ClassCourse::where('class_id', $this->class_id)
            ->where('course_id', $this->course_id)
            ->with('professors')
            ->first();
    }

    public function scopeforExams($query)
    {
        return $query->whereHas('timetable', function($query) {
            $query->where('type', 'EXAM');
        });
    }

    public function scopeforLectures($query)
    {
        return $query->whereHas('timetable', function($query) {
            $query->where('type', 'NORMAL');
        });
    }
}

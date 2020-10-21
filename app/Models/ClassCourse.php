<?php

namespace App\Models;

class ClassCourse extends Model
{
    /**
     * The DB table used by this model
     *
     * @var string
     */
    protected $table = 'courses_classes';

    /**
     * Fields that are not mass assignable
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Relationships to be loaded with this model
     *
     * @var array
     */
    protected $relations = ['course', 'professors'];

    /**
     * The college class involved
     *
     * @return App\Models\CollegeClass
     */
    public function college_class()
    {
        return $this->belongsTo(CollegeClass::class, 'class_id');
    }

    /**
     * The course involved
     *
     * @return App\Models\Course
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * The academic period this course pairing occurs in
     *
     * @return App\Models\AcademicPeriod
     */
    public function academic_period()
    {
        return $this->belongsTo(AcademicPeriod::class, 'academic_period_id');
    }

    /**
     * The professors responsible for treating this course for given class
     *
     * @return App\Models\Professor
     */
    public function professors()
    {
        return $this->belongsToMany(Professor::class, 'courses_professors', 'course_class_id', 'professor_id');
    }

    public function scopeOfferedInPeriod($query, $periodId) {
        return $query->where('academic_period_id', $periodId);
    }

    public function scopeUnscheduled($query, $timetableId)
    {
        $scheduledCourseIds = TimetableSchedule::where('timetable_id', $timetableId)
            ->pluck('course_id')
            ->toArray();


        return $query->whereNotIn('course_id', $scheduledCourseIds);
    }
}
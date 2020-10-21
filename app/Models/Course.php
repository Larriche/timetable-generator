<?php

namespace App\Models;

use DB;
class Course extends Model
{
    /**
     * The DB table used by this model
     *
     * @var string
     */
    protected $table = 'courses';

    /**
     * The fields that should not be mass assigned
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Relations of this model
     *
     * @var array
     */
    protected $relations = ['class_courses'];

    /**
     * Fields that a keyword search should be carried on
     *
     * @var array
     */
    protected $searchFields = ['name', 'course_code'];

    /**
     * Declare a relationship between this course and the classes
     * that offer it
     *
     * @return Illuminate\Database\Eloquent
     */
    public function class_courses()
    {
        return $this->hasMany(ClassCourse::class, 'course_id');
    }
}

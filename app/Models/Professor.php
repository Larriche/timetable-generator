<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;

class Professor extends Model
{
    use Notifiable;

    /**
     * DB table this model uses
     *
     * @var string
     */
    protected $table = 'professors';

    /**
     * Non-mass assignable fields
     */
    protected $guarded = ['id'];

    /**
     * Fields that a keyword search should be carried on
     *
     * @var array
     */
    protected $searchFields = ['name', 'email'];

    public function class_courses()
    {
        return $this->belongsToMany(ClassCourse::class, 'courses_professors', 'professor_id', 'course_class_id');
    }
}

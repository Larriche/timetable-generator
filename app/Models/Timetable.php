<?php

namespace App\Models;

class Timetable extends Model
{
    /**
     * Table used by this model
     *
     * @var string
     */
    protected $table = 'timetables';

    /**
     * Non mass assignable fields
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Relations of this model
     *
     * @var array
     */
    protected $relations = ['schedules'];

    /**
     * Fields that a keyword search should be carried on
     *
     * @var array
     */
    protected $searchFields = ['name'];

    /**
     * Schedules created for a timetable
     */
    public function schedules()
    {
        return $this->hasMany(TimetableSchedule::class, 'timetable_id');
    }
}

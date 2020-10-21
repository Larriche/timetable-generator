<?php

namespace App\Models;

use DB;

class ExamIncident extends Model
{
    /**
     * The DB table used by this model
     *
     * @var string
     */
    protected $table = 'exam_incidents';

    /**
     * Schedule for this incident record
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function schedule()
    {
        return $this->belongsTo(TimetableSchedule::class, 'schedule_id');
    }
}
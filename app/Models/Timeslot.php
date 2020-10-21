<?php

namespace App\Models;

class Timeslot extends Model
{
    protected $table = 'timeslots';

    protected $guarded = ['id'];

    /**
     * Fields that a keyword search should be carried on
     *
     * @var array
     */
    protected $searchFields = ['label'];

    /**
     * Determine whether a given period  is within
     * the period of this timeslot
     *
     * @param string $timePeriod The time period
     * @return Boolean Value of test
     */
    public function containsPeriod($timePeriod)
    {
        $edgesA = self::getParts($this->time);
        $edgesB = self::getParts($timePeriod);

        return (($edgesB[0] >= $edgesA[0]) && $edgesB[2] <= $edgesA[2]);
    }

    /**
     * Get the beginning and end of a given time period
     *
     * @param string $timePeriod Time period
     * @return array Parts of given time period
     */
    public static function getParts($timePeriod)
    {
        preg_match('/(0?\d{1,2}):(\d{2})\s*\-\s*(\d{2}):(\d{2})/', $timePeriod, $matches);

        return array_slice($matches, 1);
    }

    /**
     * Generate a time period string
     *
     * @param string $from From section of period
     * @param string $to   To section of period
     */
    public static function createTimePeriod($from, $to)
    {
        return $from . ' - ' . $to;
    }

    /**
     * Timeslots that are for exams
     */
    public function scopeForExams($query)
    {
        return $query->where('type', 'EXAM');
    }

    /**
     * Timeslots that are for lectures
     */
    public function scopeForLectures($query)
    {
        return $query->where('type', 'NORMAL');
    }
}

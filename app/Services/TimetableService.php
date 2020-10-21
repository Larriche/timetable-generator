<?php
namespace App\Services;

use DB;

use App\Models\Course;
use App\Models\Timetable;
use App\Models\Professor;
use App\Models\CollegeClass;


class TimetableService extends AbstractService
{
    /*
     * The model to be used by this service.
     *
     * @var \App\Models\Room
     */
    protected $model = Timetable::class;

    /**
     * Show resources with their relations.
     *
     * @var bool
     */
    protected $showWithRelations = true;

    /**
     * Weekdays
     *
     * @var array
     */
    public const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    /**
     * Check that everything is intact to create a timetable set
     * Return errors from check
     *
     * @return array Errors from check
     */
    public function checkCreationConditions()
    {
        $errors = [];

        if (!CollegeClass::count()) {
            $errors[] = "No classes have been added";
        }

        $classesQuery = 'SELECT id FROM classes WHERE id NOT IN (SELECT DISTINCT class_id FROM courses_classes)';
        $classIds = DB::select(DB::Raw($classesQuery));

        if (count($classIds)) {
            $errors[] = "Some classes don't have any course set up.<a href=\"/classes?filter=no_course\" target=\"_blank\">Click here to review them</a>";
        }

        return $errors;
    }

    /**
     * Generate tabular data for a timetable based on passed params
     * to determine mode of view
     *
     * @param App\Models\Timetable $table The timetable whose data we are viewing
     * @param array $params Params for generating timetable
     * @return array Data extracted from schedules
     */
    public function getTimetableData($timetable, $params)
    {
        $class_id = array_get($params, 'class_id', null);
        $professor_id = array_get($params, 'professor_id', null);
        $course_id = array_get($params, 'course_id', null);
        $date = array_get($params, 'date', null);

        $schedules = $timetable->schedules();

        if ($class_id) {
            $schedules = $schedules->where('class_id', $class_id);
        }

        if ($date) {
            $schedules = $schedules->where('day', '=', $date);
        }

        if ($course_id) {
            $schedules = $schedules->where('course_id', $course_id);
        }

        if ($professor_id && $timetable->type == 'EXAM') {
            // Filter exam timetable schedules out for an invigilator
            $schedules = $schedules->whereHas('invigilators', function($query) use ($professor_id) {
                $query->where('professors.id', $professor_id);
            });
        }

        $schedules = $schedules->orderBy('day', 'ASC')
            ->orderBy('timeslot_id', 'ASC')
            ->get();

        if ($professor_id && $timetable->type == 'NORMAL') {
            // Filter for normal lectures timetables
            $schedules = $schedules->filter(function ($schedule) use ($professor_id) {
                $professor_ids = $schedule->class_course->professors()->pluck('professors.id')->toArray();

                return in_array($professor_id, $professor_ids);
            });
        }

        $data = [];

        foreach ($schedules as $schedule) {
            if ($timetable->type == 'EXAM') {
                if (!isset($data[$schedule->day][$schedule->timeslot->label][$schedule->course_id])) {
                    $data[$schedule->day][$schedule->timeslot->label][$schedule->course_id] = [
                        'course' => $schedule->course->course_code . ' ' . $schedule->course->name,
                        'schedules' => []
                    ];
                }

                $data[$schedule->day][$schedule->timeslot->label][$schedule->course_id]['schedules'][] = $schedule;
            } else {
                if (!isset($data[$schedule->class->name][$schedule->day][$schedule->timeslot->label])) {
                    $data[$schedule->class->name][$schedule->day][$schedule->timeslot->label] = [];
                }

                $data[$schedule->class->name][$schedule->day][$schedule->timeslot->label][] = [
                    'course' => $schedule->course,
                    'lecturers' => $schedule->class_course->professors,
                    'room' => $schedule->rooms()->first()->name
                ];

                ksort($data);
            }
        }

        return $data;
    }
}
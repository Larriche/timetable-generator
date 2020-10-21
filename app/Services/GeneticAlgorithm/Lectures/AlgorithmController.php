<?php

namespace App\Services\GeneticAlgorithm\Lectures;

use Carbon\Carbon;

use App\Models\Room;
use App\Models\Course;
use App\Models\Timeslot;
use App\Models\Professor;
use App\Models\Timetable;
use App\Models\CollegeClass;
use App\Models\ClassCourse;

class AlgorithmController
{
    /**
     * Length of chromosomes for individuals in the genetic algorithm runs
     *
     * @var int
     */
    public $chromosome_length;

    /**
     * Timetable model entity for getting details about timetable
     *
     * @var App\Models\Timetable
     */
    public $timetable;

    /**
     * A mapping for reading chromosome data
     *
     * @var string
     */
    public $scheme;

    public $classes;

    /**
     * Create a new instance of this class
     *
     * @param App\Models\Timetable $timetable Instance of timetable data item
     */
    public function __construct($timetable, $classes)
    {
        $this->timetable = $timetable;
        $this->classes = $classes;
    }

    /**
     * Create a new individual for the GA runs
     *
     * @return array $chromsosome Chromosome of generated individual
     */
    public function create_individual()
    {
        $timetable = $this->timetable;
        $chromosome = [];
        $this->scheme = "";

        $all_days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        ];

        $exempted_days = explode(",", $timetable->days_exempted);
        $available_days = [];

        foreach ($all_days as $id => $day) {
            if (!in_array($day, $exempted_days)) {
                $available_days[$id] = $day;
            }
        }

        $periods = Timeslot::forLectures()->get();

        foreach ($this->classes as $class) {
            $rooms = $class->block->rooms()->get();

            if (!count($rooms)) {
                continue;
            }

            $courses = $class->courses()->offeredInPeriod($timetable->academic_period_id)
                ->get();

            foreach ($courses as $course) {
                for ($i = 1; $i <= $course->credits; $i++) {
                    $day_gene = 'SD' . array_rand($available_days);
                    $day_gene .= 'T' .  $periods->random(1)[0]->id;

                    $chromosome[] = $day_gene;
                    $chromosome[] = $rooms->random(1)[0]->id;

                    $this->scheme .= $class->id . '|' . $course->course_id . ',';
                }
            }
        }

        $this->scheme = trim($this->scheme, ',');

        return $chromosome;
    }

    /**
     * Calculate fitness of an individual
     *
     * @param \Adilarry\GALib\Individual $individual Individual to get fitness for
     * @return double Fitness of individual
     */
    public function calculate_fitness($individual)
    {
        $data = Helper::parseChromosome((string) $individual, $this->scheme);

        return $this->get_fitness_score($individual, $data);
    }

    public function get_fitness_score($individual, $data)
    {
        $schedules_count = 0;
        $good_schedules = 0;

        $individual_fault_log = [];

        foreach ($data as $day => $actual_day_info) {
            foreach ($actual_day_info as $period => $schedules) {
                $class_business_map = [];
                $room_business_map = [];
                $lecturer_business_map = [];

                foreach ($schedules as $schedule_index => $schedule) {
                    if (isset($schedule['new_day'])) {
                        $actual_day = $schedule['new_day'];
                    } else {
                        $actual_day = $day;
                    }

                    if (isset($schedule['new_period'])) {
                        $actual_period = $schedule['new_period'];
                    } else {
                        $actual_period = $period;
                    }

                    $faults_log = [
                        'class_capacity' => [],
                        'room_occupied' => [],
                        'class_busy' => [],
                        'lecturer_busy' => []
                    ];

                    $at_fault = false;
                    $schedules_count++;

                    // Check for room size constraint satisfaction
                    $room = Room::find($schedule['room']);
                    $class_course = ClassCourse::where('course_id', $schedule['course'])
                        ->where('class_id', $schedule['class'])
                        ->first();

                    if ($class_course && $room) {
                        if ($class_course->size > $room->capacity) {
                            $at_fault = true;

                            $faults_log['class_capacity'][] = [
                                'class' => $schedule['class'],
                                'room' => $schedule['room']
                            ];
                            //logger('room capacity');
                        }
                    }

                    // Check whether class already has a schedule during this period
                    if (isset($class_business_map[$schedule['class']])) {
                        $at_fault = true;
                        $faults_log['class_busy'] = [
                            'period' => $actual_period,
                            'class' => $schedule['class'],
                            'day' => $actual_day
                        ];
                        //logger('class occupied');
                    }

                    // Check whether a room is already occupied during this period
                    // in this current timetable generation
                    if (isset($room_business_map[$schedule['room']])) {
                        $at_fault = true;
                        $faults_log['room_occupied'][] = [
                            'period' => $actual_period,
                            'class' => $schedule['class'],
                            'day' => $actual_day,
                            'room' => $schedule['room']
                        ];
                        //logger('room occupied');
                    }

                    $days = [
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        7 => 'Sunday'
                    ];

                    $schedule_day = $days[$actual_day];
                    $room_occupied = $this->timetable->schedules()
                        ->where('timeslot_id', $actual_period)
                        ->where('day', $schedule_day)
                        ->whereHas('rooms', function($query) use ($schedule) {
                            $query->where('rooms.id', $schedule['room']);
                        })
                        ->first();

                    if ($room_occupied) {
                        $at_fault = true;

                        $faults_log['room_occupied'][] = [
                            'period' => $actual_period,
                            'class' => $schedule['class'],
                            'day' => $actual_day,
                            'room' => $schedule['room']
                        ];
                    }

                    // Check lecturer availability
                    $class_course = ClassCourse::where('course_id', $schedule['course'])
                        ->where('class_id', $schedule['class'])
                        ->first();

                    $lecturer_ids = [];

                    if ($class_course) {
                        $lecturer_ids = $class_course->professors()->pluck('professors.id')->toArray();
                    }

                    $lecturer_fault = false;

                    foreach ($lecturer_ids as $lecturer_id) {
                        $lecturer = Professor::find($lecturer_id);

                        if (isset($lecturer_business_map[$lecturer_id])) {
                            $at_fault = true;
                            $lecturer_fault = true;
                            break;
                        }

                        if ($lecturer) {
                            $lecturer_courses = $lecturer->class_courses()->get();

                            foreach ($lecturer_courses as $lecturer_course) {
                                $lecturer_busy = $this->timetable->schedules()
                                    ->where('timeslot_id', $actual_period)
                                    ->where('day', $schedule_day)
                                    ->where('course_id', $lecturer_course->course_id)
                                    ->where('class_id', $lecturer->class_id)
                                    ->first();

                                if ($lecturer_busy) {
                                    $at_fault = true;
                                    break;
                                }
                            }
                        }

                        if ($lecturer_fault) {
                            $faults_log['lecturer_busy'][] = [
                                'lecturers' => $lecturer_ids,
                                'period' =>  $actual_period
                            ];
                        }

                        $lecturer_business_map[$lecturer_id] = true;
                    }

                    // Update class business map
                    $class_business_map[$schedule['class']] = true;

                    // Update room occupation map
                    $room_business_map[$schedule['room']] = true;

                    if (!$at_fault) {
                        $good_schedules++;
                    }

                    $individual_fault_log[$actual_day][$actual_period][$schedule_index] = $faults_log;
                }
            }
        }

        $individual->set_faults_log($individual_fault_log);

        return $good_schedules / $schedules_count;
    }

    public function should_terminate($population)
    {
        return $population->get_fittest(0)->get_fitness() == 1;
    }

    public function get_scheme()
    {
        return $this->scheme;
    }


    public function log($message)
    {
        logger($message);
    }
}
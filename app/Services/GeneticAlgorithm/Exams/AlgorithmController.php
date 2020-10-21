<?php

namespace App\Services\GeneticAlgorithm\Exams;

use Carbon\Carbon;

use App\Models\Room;
use App\Models\Course;
use App\Models\Timeslot;
use App\Models\Professor;
use App\Models\Timetable;
use App\Models\CollegeClass;

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

    /**
     * Create a new instance of this class
     *
     * @param App\Models\Timetable $timetable Instance of timetable data item
     */
    public function __construct($timetable)
    {
        $this->timetable = $timetable;
        $classes_count = CollegeClass::count();

        $encoded_parts = 3;
        $this->chromosome_length = $encoded_parts * $classes_count;
    }

    /**
     * Create a new individual for the GA runs
     *
     * @return array $chromsosome Chromosome of generated individual
     */
    public function create_individual()
    {
        $chromosome = [];
        $timetable = $this->timetable;

        $days = [];
        $lecturers = Professor::all();
        $rooms = Room::all();
        $periods = Timeslot::forExams()->get();
        $classes = CollegeClass::all();

        $this->scheme = "";

        foreach ($classes as $class) {
            $courses = $class->courses()->offeredInPeriod($timetable->academic_period_id)
                ->unscheduled($timetable->id)
                ->get();

            if (!count($courses)) {
                continue;
            }

            $courses = $courses->random(1);

            $course = $courses[0];

            $this->scheme .= $class->id . '|';

            $course_ids = [];

            $course_ids[] = $course->id;
            $chromosome[] = 'SP' . $periods->random(1)[0]->id;

            $room_ids = [];
            $seated = 0;
            $class_size = (int) $course->size;

            while ($seated < $class_size) {
                $room = $rooms->random(1)->first();
                $room_size = (int)$room->capacity;

                if (in_array($room->id, $room_ids)) {
                    continue;
                }

                $seated += $room_size / 2;
                $room_ids[] = $room->id;

                foreach ($room->adjacent_rooms as $extra_room) {
                    if ($seated >= $class_size) {
                        break;
                    }

                    $extra_room_size = (int)$extra_room->capacity;
                    $seated += $extra_room_size / 2;
                    $room_ids[] = $extra_room->id;
                }
            }

            $chromosome[] = 'R' . implode(",", $room_ids);
            $chromosome[] = 'E' . implode(",", $lecturers->random($timetable->invigilators_count)->pluck('id')->toArray());

            $this->scheme .= $course->course_id . ',';
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
        $faults = 0;

        $faults_map = [
            'invigilator_fail' => false,
            'class_business' => false
        ];

        $class_exams_map = [];

        foreach ($data as $period => $infos) {
            $busy_invigilators = [];
            $room_business = [];

            foreach ($infos as $info) {
                // No lecturer should be made an invigilator at two places
                // at same time
                foreach ($info['invigilators'] as $invigilator) {
                    if (isset($busy_invigilators[$invigilator])) {
                        $faults_map['invigilator_fail'] = true;
                    } else {
                        $busy_invigilators[$invigilator] = true;
                    }
                }

                foreach ($info['rooms'] as $room => $attendance) {
                    $room_business[$room] = $attendance;
                }
            }

            // Each room should have two classes there
            foreach ($room_business as $room => $occupancy) {
                if ($occupancy < 2) {
                    $faults_map['class_business'] = true;
                }
            }
        }

        foreach ($faults_map as $fault => $status) {
            if ($status) {
                $faults++;
            }
        }

        return 1.0 / ($faults + 1);
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
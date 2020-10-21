<?php

namespace App\Services\GeneticAlgorithm\Lectures;

use App\Models\TimetableSchedule;
use App\Models\Room;
use App\Models\Course;
use App\Models\Timeslot;
use App\Models\Professor;
use App\Models\Timetable;
use App\Models\CollegeClass;
use App\Models\ClassCourse;
class Helper
{
    protected const DAYS =  [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday'
    ];

    /**
     * This helper method when given the chromosome for a candidate solution
     * to the timetable generation problem, parses the chromosome to extract
     * the data represented by the chromosome string
     *
     * @param string $chromosome Chromosome to be parsed
     * @param string $scheme A map for knowing which chromosome sections represent which class schedule
     * @return array Extracted $data
     */
    public static function parseChromosome($chromosome, $scheme)
    {
        $data = [];
        $scheme_parts = explode(",", $scheme);

        $parts = array_values(array_filter(explode('S', $chromosome), function ($value) {
            return strlen(trim($value)) > 0;
        }));

        $schedule_regex = '/^D(?P<dayId>\d)T(?P<periodId>\d+),(?P<roomId>\d+)/';

        foreach ($parts as $index => $part) {
            $part = trim($part, ',');

            $curr_scheme_parts = explode("|", $scheme_parts[$index]);
            $class = $curr_scheme_parts[0];
            $course = $curr_scheme_parts[1];

            preg_match($schedule_regex, $part, $matches);

            if (!isset($matches['dayId']) || !isset($matches['periodId']) || !isset($matches['roomId'])) {
                continue;
            }

            $day = $matches['dayId'];
            $period = $matches['periodId'];
            $room = $matches['roomId'];

            if (!isset($data[$day][$period])) {
                $data[$day][$period] = [];
            }

            $data[$day][$period][] = [
                'room' => $room,
                'class' => $class,
                'course' => $course
            ];
        }

        return $data;
    }

    /**
     * Save generated timetable schedules
     *
     * @param int $timetable_id Id of generated timetable
     * @param array $data Timetable schedules
     * @param \App\Services\GeneticAlgorithm\Lectures\AlgorithmController $controller
     */
    public static function saveSchedule($timetable_id, $data, $solution, $controller)
    {
        $days = self::DAYS;
        $timetable = Timetable::find($timetable_id);

        if ($solution->get_fitness() < 1) {
            $class_map_data = self::correctTimetable($timetable, $data, $controller->classes);
            logger('fitness of new timetable');
            logger($controller->get_fitness_score($solution, self::convertClassMapToFitnessFormat($class_map_data)));
        } else {
            $class_map_data = self::convertToClassMap($timetable, $data, $controller->classes);
        }

        foreach ($class_map_data as $class => $class_data) {
            foreach ($class_data as $day => $day_data) {
                foreach ($day_data as $period => $schedules) {
                    foreach ($schedules as $schedule_data) {
                        $schedule = TimetableSchedule::create([
                            'timetable_id' => $timetable->id,
                            'timeslot_id' => $period,
                            'day' => self::DAYS[$day],
                            'course_id' => $schedule_data['course'],
                            'class_id' => $schedule_data['class']
                        ]);

                        $room = (int) $schedule_data['room'];

                        $schedule->rooms()->sync([$room]);
                    }
                }
            }
        }
    }

    public static function correctTimetable($timetable, $data, $classes)
    {
        $class_map_data = self::convertToClassMap($timetable, $data, $classes);
        $ok = false;
        $passes = 0;

        while (!$ok && ($passes <= 5000)) {
            $ok = true;
            logger('starting again');
            foreach ($class_map_data as $class => $class_data) {
                if (!$ok) {
                    break;
                }

                foreach ($class_data as $day => $day_data) {
                    if (!$ok) {
                        break;
                    }

                    foreach ($day_data as $period => $schedules) {
                        // More than one schedule in a slot
                        // Move schedule to another slot
                        if (count($schedules) > 1) {
                            $first = true;

                            foreach ($schedules as $i => $schedule) {
                                if ($first) {
                                    $first = false;
                                    continue;
                                }

                                $new_pos = self::getNewSlot($class_map_data, $class, $day, $period, $schedule);

                                if ($new_pos) {
                                    $new_day = $new_pos['day'];
                                    $new_period = $new_pos['period'];

                                    $class_map_data[$class][$new_day][$new_period][] = $schedules[$i];
                                    unset($class_map_data[$class][$day][$period][$i]);
                                    $ok = false;
                                    logger('not ok.starting again');
                                    break;
                                }
                            }

                            if (!$ok) {
                                break;
                            }
                        }

                        foreach ($schedules as $i => $schedule) {
                            // Room fixes
                            $room = Room::find($schedule['room']);

                            if (!$room) {
                                continue;
                            }

                            if ($schedule['size'] > $room->capacity || self::isOccupiedRoom($class_map_data, $day, $period, $room->id,$class)) {
                                $new_room = self::getRoom($class_map_data, $day, $period, $schedule);

                                if ($new_room) {
                                    logger('new_room');
                                    logger($i);
                                    logger($class_map_data[$class][$day][$period]);
                                    logger($class_map_data[$class][$day][$period][$i]);
                                    $class_map_data[$class][$day][$period][$i]['room'] = $new_room;
                                    $ok = false;
                                    break;
                                }
                            }

                            // Ensure lecturer is not busy elsewhere
                            if (self::lecturerBusyElsewhere($class_map_data, $day, $period, $class, $schedule)) {
                                $new_pos = self::getNewSlot($class_map_data, $class, $day, $period, $schedule);

                                if ($new_pos) {
                                    $new_day = $new_pos['day'];
                                    $new_period = $new_pos['period'];

                                    $class_map_data[$class][$new_day][$new_period][] = $schedule;
                                    unset($class_map_data[$class][$day][$period][$i]);
                                    $ok = false;
                                    logger('not ok.starting again');
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            $passes++;
        }

        if (!$ok) {
            logger('passes timed out');
        }


        return $class_map_data;
    }

    public static function convertToClassMap($timetable, $data, $classes)
    {
        $class_map = [];
        $periods = Timeslot::forLectures()->pluck('id')->toArray();
        $exempted_days = explode(",", $timetable->days_exempted);

        $days = self::DAYS;

        foreach ($classes as $class) {
            foreach ($days as $day_id => $day) {
                if (!in_array($day, $exempted_days)) {
                    foreach ($periods as $period) {
                        $class_map[$class->id][$day_id][$period] = [];
                    }
                }
            }
        }

        foreach ($data as $day => $day_info) {
            foreach ($day_info as $period => $schedules) {
                foreach ($schedules as $schedule_data) {
                    $course_id = $schedule_data['course'];
                    $class_id = $schedule_data['class'];
                    $room_id = $schedule_data['room'];

                    $class_course = ClassCourse::where('course_id', $course_id)
                        ->where('class_id', $class_id)
                        ->first();

                    $lecturer_ids = [];

                    if ($class_course) {
                        $lecturer_ids = $class_course->professors()->pluck('professors.id')->toArray();
                    }

                    $class_map[$class_id][$day][$period][] = [
                        'course' => $course_id,
                        'class' => $class_id,
                        'room' => $room_id,
                        'lecturers' => $lecturer_ids,
                        'size' => $class_course->size
                    ];
                }
            }
        }

        return $class_map;
    }

    public static function convertClassMapToFitnessFormat($map)
    {
        $data = [];

        foreach ($map as $class => $class_data) {
            foreach ($class_data as $day => $day_data) {
                foreach ($day_data as $period => $schedules) {
                    foreach ($schedules as $schedule_data) {
                        $room = $schedule_data['room'];
                        $course = $schedule_data['course'];

                        if (!isset($data[$day][$period])) {
                            $data[$day][$period] = [];
                        }

                        $data[$day][$period][] = [
                            'room' => $room,
                            'class' => $class,
                            'course' => $course
                        ];
                    }
                }
            }
        }

        return $data;
    }

    public static function getNewSlot($map, $class, $day, $period, $schedule, $free_room = false)
    {
        foreach ($map[$class] as $map_day => $day_data) {
            foreach ($day_data as $map_period => $schedules) {
                if (!count($schedules) && self::freeForLecturers($map, $map_day, $map_period, $schedule['lecturers'])) {
                    if ($free_room) {
                        $room_occupied = self::isOccupiedRoom($map, $map_day, $map_period, $schedule['room'], $class);

                        if (!$room_occupied) {
                            return [
                                'period' => $map_period,
                                'day' => $map_day
                            ];
                        }
                    }

                    return [
                        'period' => $map_period,
                        'day' => $map_day
                    ];
                }
            }
        }

        return null;
    }

    public static function freeForLecturers($map, $day, $period, $lecturers)
    {
        foreach ($map as $class => $class_data) {
            $period_schedules = $class_data[$day][$period];

            foreach ($lecturers as $lecturer) {
                foreach ($period_schedules as $schedule) {
                    if (in_array($lecturer, $schedule['lecturers'])) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public static function getRoom($map, $day, $period, $the_schedule)
    {
        $occupied_rooms = [];
        $size = $the_schedule['size'];

        foreach ($map as $class => $class_data) {
            $period_schedules = $class_data[$day][$period];

            foreach ($period_schedules as $schedule) {
                $occupied_rooms[] = $schedule['room'];
            }
        }

        $class = CollegeClass::find($the_schedule['class']);

        if (!$class) {
            return null;
        }

        $room = Room::whereNotIn('id', $occupied_rooms)
             ->where('block_id', $class->block_id)
             ->where('capacity', '>=', $size)
             ->inRandomOrder()
             ->first();

        logger('looking for a room');
        logger($room);

        return $room ? $room->id : null;
    }

    public static function isOccupiedRoom($map, $day, $period, $room, $class)
    {
        foreach ($map as $curr_class => $class_data) {
            $period_schedules = $class_data[$day][$period];

            foreach ($period_schedules as $schedule) {
                if ($schedule['room'] == $room && $curr_class != $class) {
                    logger('room occupied for classes ' . $class . ' and ' . $curr_class . ' day ' . $day . ' period ' . $period);
                    return true;
                }
            }
        }

        return false;
    }

    public static function lecturerBusyElsewhere($map, $day, $period, $class, $suspect_schedule)
    {
        $lecturers = $suspect_schedule['lecturers'];

        foreach ($map as $class => $class_data) {
            $period_schedules = $class_data[$day][$period];

            foreach ($lecturers as $lecturer) {
                foreach ($period_schedules as $schedule) {
                    if (in_array($lecturer, $schedule['lecturers']) && $schedule['class'] != $class) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
<?php
namespace App\Services\GeneticAlgorithm\Exams;

use App\Models\TimetableSchedule;
class Helper
{
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

        // Use date limiter as splitting point for different schedules
        $parts = array_values(array_filter(explode('S', $chromosome), function ($value) {
            return strlen(trim($value)) > 0;
        }));

        $schedule_regex = '/^P(?P<period>\d+),R(?P<rooms>(\d,?)+),E(?P<invigilators>((\d)*,)*(\d)*)/';

        $class = '';

        foreach ($parts as $index => $part) {
            if (!trim($part)) {
                continue;
            }

            $part = trim($part, ',');

            $curr_scheme_parts = explode("|", $scheme_parts[$index]);

            if (!isset($curr_scheme_parts[0]) || !isset($curr_scheme_parts[1])) {
                continue;
            }

            $class = $curr_scheme_parts[0];
            $course = $curr_scheme_parts[1];

            preg_match($schedule_regex, $part, $matches);

            if (!isset($matches['period']) || !isset($matches['rooms']) || !isset($matches['invigilators'])) {
                continue;
            }

            $period = $matches['period'];

            if (!isset($data[$period])) {
                $data[$period] = [];
            }

            $schedule = [
                'rooms' => explode(",", $matches['rooms']),
                'invigilators' => explode(",", $matches['invigilators']),
                'class' => $class,
                'course' => $course
            ];

            $data[$period][] = $schedule;
        }

        return $data;
    }

    /**
     * Store exam schedule information into the database
     *
     * @param int $timetable_id Id of timetable
     * @param string $data Scheduling's date
     * @param array $data Data for schedule
     */
    public static function saveSchedule($timetable_id, $date, $data)
    {
        foreach ($data as $period_id => $period_data) {
            foreach ($period_data as $index => $schedule_data) {
                $schedule = TimetableSchedule::create([
                    'timetable_id' => $timetable_id,
                    'timeslot_id' => $period_id,
                    'day' => $date,
                    'course_id' => $schedule_data['course'],
                    'class_id' => $schedule_data['class']
                ]);

                $schedule->invigilators()->sync($schedule_data['invigilators']);
                $schedule->rooms()->sync($schedule_data['rooms']);
            }
        }
    }
}
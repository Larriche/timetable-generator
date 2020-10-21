<?php

use Illuminate\Database\Seeder;

use App\Models\Room;
use App\Models\Block;
use App\Models\Course;
use App\Models\Professor;
use App\Models\ClassCourse;
use App\Models\CollegeClass;

class CollegeOfScienceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $contents = File::get(base_path() . '/database/seeds/rooms.txt');
            $lines = explode("\n", $contents);

            foreach ($lines as $line) {
                $parts = explode("||", $line);

                $block_name = isset($parts[0]) ? $parts[0] : "";
                $rooms = $parts[1];

                $block = Block::where('name', $block_name)->first();

                if (!$block) {
                    $block = Block::create([
                        'name' => $block_name
                    ]);
                }

                $room_groups = explode(",", $rooms);

                foreach ($room_groups as $room_group) {
                    $room_ids = [];
                    $room_infos = explode("|", $room_group);

                    foreach ($room_infos as $room_info) {
                        $room_regex = '/^(?P<room>[\w\s]*)\((?P<size>\d*)\)/';
                        preg_match($room_regex, $room_info, $matches);

                        $room_name = trim($matches['room']);
                        $size = trim($matches['size']);

                        $room = Room::where('name', $room_name)->first();

                        if (!$room) {
                            $room = Room::create([
                                'name' => $room_name,
                                'capacity' => $size,
                                'block_id' => $block->id
                            ]);
                        }

                        $room_ids[] = $room->id;
                    }

                    foreach ($room_ids as $id) {
                        foreach ($room_ids as $adj_id) {
                            if ($id != $adj_id) {
                                $existing = DB::table('adjacent_rooms')
                                    ->where('room_id', $id)
                                    ->where('adjacent_room_id', $adj_id)
                                    ->first();

                                    if (!$existing) {
                                        DB::table('adjacent_rooms')->insert([
                                            'room_id' => $id,
                                            'adjacent_room_id' => $adj_id
                                        ]);
                                    }
                            }
                        }
                    }
                }
            }
        } catch (Illuminate\Contracts\Filesystem\FileNotFoundException $exception) {
            die("The rooms data file doesn't exist");
        }

        try {
            $contents = File::get(base_path() . '/database/seeds/cos_data.txt');
            $lines = explode("\n", $contents);

            foreach ($lines as $line) {
                if (starts_with($line, '*')) {
                    continue;
                }

                $sections = explode(" | ", $line);

                $course_code = isset($sections[0]) ? $sections[0] : null;
                $course_name = isset($sections[1]) ? $sections[1] : null;
                $lecturer_info = isset($sections[2]) ? $sections[2] : null;

                $lecturer_sections = explode(' || ', $lecturer_info);

                $course = Course::where('course_code', $course_code)
                    ->where('name', $course_name)
                    ->first();

                if (!$course) {
                    $course = Course::create([
                        'course_code' => $course_code,
                        'name' => $course_name
                    ]);

                }

                foreach ($lecturer_sections as $lecturer_section) {
                    $parts = explode("=", $lecturer_section);
                    $lecturers_str = (isset($parts[0])) ? trim($parts[0]) : "";
                    $classes_str = (isset($parts[1])) ? trim($parts[1]) : "";

                    if ($lecturers_str == 'N/A') {
                        $lecturers = [null];
                    } else {
                        $lecturers = explode("/", $lecturers_str);
                    }

                    $classes = explode(",", $classes_str);
                    $lecturer_ids = [];
                    $class_ids = [];

                    foreach ($lecturers as $lecturer_name) {
                        if (!$lecturer_name) {
                            continue;
                        }

                        $lecturer_name = trim($lecturer_name);
                        $lecturer = Professor::where('name', $lecturer_name)->first();

                        if (!$lecturer) {
                            $lecturer = Professor::create(['name' => $lecturer_name, 'email' => '']);
                        }

                        $lecturer_ids[] = $lecturer->id;
                    }

                    foreach ($classes as $class_info) {
                        $class_info = trim($class_info);

                        if (!$class_info) {
                            continue;
                        }

                        $class_regex = '/^(?P<class>[\w\s]*)\((?P<size>\d*)\)/';

                        preg_match($class_regex, $class_info, $matches);

                        if (!isset($matches['class']) || !isset($matches['size'])) {
                            logger($class_info);
                            continue;
                        }

                        $class = CollegeClass::where('name', $matches['class'])->first();

                        if (preg_match('/^C[^S]\d*/', $matches['class'])) {
                            $block = Block::where('name', 'Kufuor Block')->first();
                        } else {
                            $block = Block::where('name', 'Aboagye Menyeh Complex')->first();
                        }

                        $block_id = $block ? $block->id : null;

                        if (!$class) {
                            $class = CollegeClass::create([
                                'name' => $matches['class'],
                                'block_id' => $block_id
                            ]);
                        }

                        $credits = [1,2];

                        $course_pairing = ClassCourse::where('course_id', $course->id)
                            ->where('class_id', $class->id)
                            ->first();

                        if (!$course_pairing) {
                            $pairing = ClassCourse::create([
                                'course_id' => $course->id,
                                'class_id' => $class->id,
                                'size' => $matches['size'],
                                'academic_period_id' => 1,
                                'credits' => $credits[array_rand($credits)]
                            ]);

                            $pairing->professors()->sync($lecturer_ids);
                        }
                    }
                }
            }

        } catch (Illuminate\Contracts\Filesystem\FileNotFoundException $exception) {
            die("The cos data file doesn't exist");
        }
    }
}

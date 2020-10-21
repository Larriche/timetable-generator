<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Course;
use App\Models\Professor;
use App\Models\CollegeClass;

class DashboardService
{
    /**
     * Get data for display on the dashboard
     *
     * @return array Data
     */
    public function getData()
    {
        $roomsCount = Room::count();
        $coursesCount = Course::count();
        $professorsCount = Professor::count();
        $classesCount = CollegeClass::count();

        $data = [
            'cards' => [
                [
                    'title' => 'Lecture Rooms',
                    'icon' => 'home',
                    'value' => $roomsCount,
                    'url' => '/rooms',
                    'color' => 'red'
                ],
                [
                    'title' => 'Courses',
                    'icon' => 'book',
                    'value' => $coursesCount,
                    'url' => '/courses',
                    'color' => 'green'
                ],
                [
                    'title' => 'Lecturers',
                    'icon' => 'graduation-cap',
                    'value' => $professorsCount,
                    'url' => '/professors',
                    'color' => 'teal'
                ],
                [
                    'title' => 'Classes',
                    'icon' => 'users',
                    'value' => $classesCount,
                    'url' => '/classes',
                    'color' => 'blue'
                ]
            ]
        ];

        return $data;
    }
}
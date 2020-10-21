<?php

namespace App\Services;

use DB;
use App\Models\Course;
use App\Models\ClassCourse;

class ClassCoursesService extends AbstractService
{
    /*
     * The model to be used by this service.
     *
     * @var \App\Models\CollegeClass
     */
    protected $model = ClassCourse::class;

    /**
     * Add a new class course
     *
     * @param array $data Data
     */
    public function store($data = [])
    {
        $course = parent::store($data);

        if ($course) {
            $course->professors()->sync($data['professor_ids']);
        }

        return $course;
    }

    /**
     * Get a given course class
     *
     * @param int $id Id of course class pairing
     */
    public function show($id)
    {
        $course = parent::show($id);
        $course->professor_ids = $course->professors->pluck('id');

        return $course;
    }

    /**
     * Update a class course pairing
     *
     * @param int $id Id of class course pairing
     * @param array $data Data for update
     */
    public function update($id, $data = [])
    {
        $course = parent::update($id, $data);

        if ($course) {
            $course->professors()->sync($data['professor_ids']);
        }

        return $course;
    }

    /**
     * Search course
     *
     * @param string $query Query so far
     * @param string $keyword Keyword to search for
     */
    public function search($query, $keyword)
    {
        $model = new $this->model;

        $query = $query->whereHas('course', function($course_query) use ($keyword) {
            $first = true;
            $model = new Course();
            logger($model->getSearchFields());

            foreach ($model->getSearchFields() as $field) {
                if ($first) {
                    $course_query->where($field, 'LIKE', '%' . $keyword . '%');
                    $first = false;
                } else {
                    $course_query->orWhere($field, 'LIKE', '%' . $keyword . '%');
                }
            }
        });

        logger($query->toSql());

        return $query;
    }
}
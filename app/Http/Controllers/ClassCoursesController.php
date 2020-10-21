<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use App\Http\Requests\CoursesRequest;
use App\Services\ClassCoursesService;

use App\Models\Room;
use App\Models\Course;
use App\Models\Professor;
use App\Models\CollegeClass;
use App\Models\ClassCourse;
use App\Models\AcademicPeriod;

class ClassCoursesController extends Controller
{
     /**
     * Service class for handling operations relating to this
     * controller
     *
     * @var App\Services\ClassCoursesService $service
     */
    protected $service;

    /**
     * Create a new instance of this controller
     *
     * @param App\Services\CollegeCoursesService $service
     */
    public function __construct(ClassCoursesService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
        $this->middleware('activated');
    }

    /**
     * Show a listing of a class' courses
     *
     * @param \Illuminate\Http\Request $request The HTTP request
     * @param int $classId Id of college class
     */
    public function index(Request $request, $classId)
    {
        $class = CollegeClass::find($classId);

        if (!$class) {
            // return 404
        }

        $classCourses = $this->service->all([
            'keyword' => $request->has('keyword') ? $request->keyword : null,
            'filter' => $request->has('filter') ? $request->filter : null,
            'order_by' => 'course_code',
            'paginate' => 'true',
            'per_page' => 20,
            'class_id' => $classId
        ]);

        $courses = Course::orderBy('course_code', 'ASC')->get();
        $professors = Professor::orderBy('name', 'ASC')->get();
        $academicPeriods = AcademicPeriod::orderBy('name', 'ASC')->get();
        $courseIds = $class->courses()->pluck('course_id')->toArray();

        if ($request->ajax()) {
            return view('courses_manager.table', compact('classCourses'));
        }

        return view('courses_manager.index', compact('professors', 'academicPeriods', 'class', 'classCourses', 'courses', 'courseIds'));
    }

    /**
     * Add a course for a class
     *
     * @param App\Http\CoursesRequest $request
     * @param int $classId Id of class
     */
    public function store(CoursesRequest $request, $classId)
    {
        $class = CollegeClass::find($classId);

        if (!$class) {
            return Response::json(['error' => 'A system error occurred'], 500);
        }

        $existing = $class->courses()->where('course_id', $request->course_id)->first();

        if ($existing) {
            return Response::json(['errors' => ['This course has already been added for this class']], 422);
        }

        $course = $this->service->store($request->all());

        if ($course) {
            return Response::json(['message' => 'Course has been added for class'], 200);
        } else {
            return Response::json(['error' => 'A system error occurred'], 500);
        }
    }

    /**
     * Get a class course by id
     *
     * @param Illuminate\Http\Request $request HTTP request
     * @param int $classId Id of class
     * @param int id The id of the class course
     */
    public function show(Request $request, $classId, $courseId)
    {
        $course = $this->service->show($courseId);

        if ($course) {
            return Response::json($course, 200);
        } else {
            return Response::json(['error' => 'Course not found'], 404);
        }
    }

    /**
     * Update a class course pairing
     *
     * @param App\Http\CoursesRequest $request The request for managing courses
     * @param int $classId Id of college class
     * @param int $courseId Id of course
     */
    public function update(CoursesRequest $request, $classId, $courseId)
    {
        $course = $this->service->show($courseId);

        if (!$course) {
            return Response::json(['error' => 'Course not found'], 404);
        }

        $course = $this->service->update($courseId, $request->all());

        return Response::json(['message' => 'Course has been updated'], 200);
    }

    /**
     * Get a class course by id
     *
     * @param Illuminate\Http\Request $request HTTP request
     * @param int $classId Id of class
     * @param int id The id of the class course
     */
    public function destroy(Request $request, $classId, $courseId)
    {
        $course = ClassCourse::find($courseId);

        if (!$course) {
            return Response::json(['error' => 'Course not found'], 404);
        }

        if ($this->service->delete($courseId)) {
            return Response::json(['message' => 'Course has been removed'], 200);
        } else {
            return Response::json(['error' => 'An unknown system error occurred'], 500);
        }
    }


    /**
     * Get the form for adding and updating courses
     *
     * @param int $classId Id of college class
     * @return \Illuminate\Http\Response The form view
     */
    public function getForm($classId)
    {
        $class = CollegeClass::find($classId);

        if (!$class) {
            return response()->json(['message' => 'This class does not exist'], 404);
        }

        $courses = Course::orderBy('course_code', 'ASC')->get();
        $professors = Professor::orderBy('name', 'ASC')->get();
        $academicPeriods = AcademicPeriod::orderBy('name', 'ASC')->get();
        $courseIds = $class->courses()->pluck('course_id')->toArray();

        return view('courses_manager.form', compact('professors', 'academicPeriods', 'class', 'classCourses', 'courses', 'courseIds'));
    }
}
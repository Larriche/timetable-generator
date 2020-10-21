<?php

namespace App\Http\Controllers;

use PDF;
use Auth;
use Response;
use Storage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\TimetableService;
use App\Events\TimetablesRequested;

use App\Models\Day;
use App\Models\Course;
use App\Models\Timetable;
use App\Models\Timeslot;
use App\Models\Professor;
use App\Models\CollegeClass;

class TimetablesController extends Controller
{
    /**
     * Create a new instance of this controller and set up
     * middlewares on this controller methods
     */
    public function __construct(TimetableService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
        $this->middleware('activated');
    }

    /**
     * Handle ajax request to load timetable to populate
     * timetables table on dashboard
     *
     * @param \Illuminate\Http\Request $request HTTP request
     * @return \Illuminate\Http\Response The HTTP response
     */
    public function index(Request $request)
    {
        $timetables = $this->service->all([
            'keyword' => $request->has('keyword') ? $request->keyword : null,
            'order_by' => 'name',
            'paginate' => 'true',
            'per_page' => 20
        ]);

        if ($request->ajax()) {
            return view('timetables.table', compact('timetables'));
        }

        return view('timetables.index', compact('timetables'));
    }

    /**
     * Create a new timetable object and hand over to genetic algorithm
     * to generate
     *
     * @param Illuminate\Http\Request $request The HTTP request
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'type' => 'required',
            'academic_period_id' => 'required'
        ];

        if ($request->type == 'EXAM') {
            $rules['start_date'] = 'required|date';
            $rules['end_date'] = 'required|date';
            $rules['invigilators_count'] = 'required|numeric';
        }

        $messages = [
            'academic_period_id.required' => 'An academic period must be selected'
        ];

        $this->validate($request, $rules, $messages);
        $lecturers_count = Professor::count();

        if ($request->has('invigilators_count') && $request->invigilators_count > $lecturers_count) {
            return Response::json(['errors' => ['Number of invigilators available cannot exceed ' . $lecturers_count]], 422);
        }

        $errors = [];

        if (count($errors)) {
            return Response::json(['errors' => $errors], 422);
        }

        $otherChecks = $this->service->checkCreationConditions();

        if (count($otherChecks)) {
            return Response::json(['errors' => $otherChecks], 422);
        }

        $exempted_days = [];

        foreach (TimetableService::DAYS as $day) {
            if ($request->has($day)) {
                $exempted_days[] = $day;
            }
        }

        $data = [
            'user_id' => Auth::user()->id,
            'academic_period_id' => $request->academic_period_id,
            'status' => 'IN PROGRESS',
            'type' => $request->type,
            'name' => $request->name,
            'days_exempted' => implode(",", $exempted_days)
        ];

        if ($request->type == 'EXAM') {
            $data['start_date'] = Carbon::parse($request->start_date)->toDateString();
            $data['end_date'] = Carbon::parse($request->end_date)->toDateString();
            $data['invigilators_count'] = $request->invigilators_count;
        }

        $timetable = Timetable::create($data);

        event(new TimetablesRequested($timetable));

        return Response::json(['message' => 'Timetables are being generated.Check back later'], 200);
    }

    /**
     * Show rendering of timetable
     *
     * @param \Illuminate\Http\Request $request The HTTP request
     * @param int $id Id of timetable
     */
    public function view(Request $request, $id)
    {
        $timetable = Timetable::find($id);

        if ($timetable->type == 'NORMAL') {
            $timeslots = Timeslot::forLectures()->get();
        } else {
            $timeslots = Timeslot::forExams()->get();
        }

        $schedules = $this->service->getTimetableData($timetable, $request->all());

        $course_ids = $timetable->schedules()->pluck('course_id')->toArray();
        $class_ids = $timetable->schedules()->pluck('class_id')->toArray();
        $professors = Professor::all();

        $courses = Course::whereIn('id', $course_ids)->orderBy('course_code')->get();
        $classes = CollegeClass::whereIn('id', $class_ids)->orderBy('name')->get();
        $searched_day = $request->date;

        if ($request->ajax()) {
            if ($timetable->type == 'NORMAL') {
                $view = 'timetable_schedules.lectures_table';
            } else {
                $view = 'timetable_schedules.exams_table';
            }

            return view($view, compact('schedules', 'timeslots', 'timetable', 'searched_day'));
        }

        return view('timetable_schedules.index', compact('professors', 'classes', 'courses', 'timetable', 'schedules', 'timeslots', 'searched_day'));
    }

    /**
     * Print a report
     *
     * @param \Illuminate\Http\Request $request The HTTP request
     * @param int $id Id of timetable
     */
    public function print(Request $request, $id)
    {
        $timetable = Timetable::find($id);

        if ($timetable->type == 'NORMAL') {
            $timeslots = Timeslot::forLectures()->get();
            $print_view = 'timetable_schedules.print_lectures';
        } else {
            $timeslots = Timeslot::forExams()->get();
            $print_view = 'timetable_schedules.print_exams';
        }

        $searched_day = $request->date;

        $schedules = $this->service->getTimetableData($timetable, $request->all());

        $pdf = PDF::loadView($print_view, compact('timetable', 'schedules', 'timeslots', 'searched_day'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream($timetable->name . '.pdf');
    }

    /**
     * Delete timetable with the given id from the database
     *
     * @param int $timetableId Id of timetable
     * @return Illuminate\Http\Response The HTTP response
     */
    public function destroy($timetableId)
    {
        $timetable = Timetable::find($timetableId);

        if (!$timetable) {
            return Response::json(['error' => 'Timetable not found'], 404);
        }

        if ($this->service->delete($timetableId)) {
            return Response::json(['message' => 'Timetable has been deleted'], 200);
        } else {
            return Response::json(['error' => 'An unknown system error occurred'], 500);
        }
    }
}

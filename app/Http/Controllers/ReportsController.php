<?php

namespace App\Http\Controllers;

use PDF;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Professor;
use App\Models\ExamIncident;
use App\Models\TimetableSchedule;
use App\Services\ExcelExportService;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('activated');
    }

    public function index()
    {
        return view('reports.index');
    }

    public function attendance()
    {
        return view('reports.attendance');
    }

    public function incidents()
    {
        return view('reports.incidents');
    }

    public function exams()
    {
        return view('reports.exams');
    }

    public function invigilators()
    {
        $professors = Professor::orderBy('name')->get();

        return view('reports.invigilators', compact('professors'));
    }

    public function rooms()
    {
        $rooms = Room::orderBy('name')->get();

        return view('reports.rooms', compact('rooms'));
    }

    public function lecturers()
    {
        $professors = Professor::orderBy('name')->get();

        return view('reports.lecturers', compact('professors'));
    }

    public function generateAttendanceReport(Request $request)
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required'
        ];

        $this->validate($request, $rules);

        $from = Carbon::parse($request->start_date)->toDateString();
        $to = Carbon::parse($request->end_date)->toDateString();

        $schedules = $this->getSchedules($from, $to);
        $formats = ['pdf', 'excel'];

        return view('reports.attendance_table', compact('schedules', 'formats'));
    }

    public function generateIncidenceReport(Request $request)
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required'
        ];

        $this->validate($request, $rules);

        $from = Carbon::parse($request->start_date)->toDateString();
        $to = Carbon::parse($request->end_date)->toDateString();

        $incidents = $this->getIncidents($from, $to);
        $formats = ['pdf'];

        return view('reports.incidents_table', compact('incidents', 'formats'));
    }

    public function generateExamsReport(Request $request)
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required'
        ];

        $this->validate($request, $rules);

        $from = Carbon::parse($request->start_date)->toDateString();
        $to = Carbon::parse($request->end_date)->toDateString();

        $schedules = $this->getSchedules($from, $to);
        $formats = ['pdf', 'excel'];

        return view('reports.exams_table', compact('schedules', 'formats'));
    }

    public function generateInvigilatorsReport(Request $request)
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
            'professor_id' => 'required'
        ];

        $this->validate($request, $rules);

        $from = Carbon::parse($request->start_date)->toDateString();
        $to = Carbon::parse($request->end_date)->toDateString();
        $professor_id = $request->professor_id;

        $schedules = $this->getSchedulesForProfessor($from, $to, $professor_id);
        $formats = ['pdf', 'excel'];

        return view('reports.invigilators_table', compact('schedules', 'formats'));
    }

    public function generateRoomsReport(Request $request)
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
            'room_id' => 'required'
        ];

        $this->validate($request, $rules);

        $from = Carbon::parse($request->start_date)->toDateString();
        $to = Carbon::parse($request->end_date)->toDateString();
        $room_id = $request->room_id;

        $schedules = $this->getSchedulesForRoom($from, $to, $room_id);
        $formats = ['pdf', 'excel'];

        return view('reports.rooms_table', compact('schedules', 'formats'));
    }

    public function generateLecturersReport(Request $request)
    {
        $rules = [
            'professor_id' => 'required'
        ];

        $this->validate($request, $rules);

        $schedules = $this->getLecturerSchedules($request->professor_id);
        $formats = ['pdf', 'excel'];

        return view('reports.lecturers_table', compact('schedules', 'formats'));
    }

    public function exportIncidenceReport(Request $request)
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required'
        ];

        $this->validate($request, $rules);

        $from = Carbon::parse($request->start_date)->toDateString();
        $to = Carbon::parse($request->end_date)->toDateString();

        $incidents = $this->getIncidents($from, $to);
        $print = true;

        $pdf = PDF::loadView('reports.incidents_print', compact('incidents', 'from', 'to', 'print'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Incidence Report.pdf');
    }

    public function exportExamsReport(Request $request)
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required'
        ];

        $this->validate($request, $rules);

        $from = Carbon::parse($request->start_date)->toDateString();
        $to = Carbon::parse($request->end_date)->toDateString();
        $format = $request->format;

        $schedules = $this->getSchedules($from, $to);
        $print = true;

        if ($format == 'pdf') {
            $pdf = PDF::loadView('reports.exams_print', compact('schedules', 'from', 'to', 'print'))
                ->setPaper('a4', 'portrait');
        } else {
            $rows = [];

            foreach ($schedules as $schedule) {
                $rows[] = [
                    date('l, jS M, Y', strtotime($schedule->day)),
                    $schedule->course->course_code . ' ' . $schedule->course->name,
                    $schedule->class->name
                ];
            }

            $data = [
                'headers' => [
                    'Date',
                    'Exam',
                    'Class'
                ],
                'data' => $rows
            ];

            $file_name = 'Examination Sittings Report';
            $title = 'Examination sittings report from ' . date('l, jS M, Y', strtotime($from)) . ' to ' . date('l, jS M, Y', strtotime($to));

            $excel_service = new ExcelExportService($file_name, $data, $title);

            return $excel_service->downloadExcel();
        }

        return $pdf->stream('Exams Report.pdf');
    }

    public function exportAttendanceReport(Request $request)
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
            'format' => 'required'
        ];

        $this->validate($request, $rules);

        $from = Carbon::parse($request->start_date)->toDateString();
        $to = Carbon::parse($request->end_date)->toDateString();
        $format = $request->format;

        $schedules = $this->getSchedules($from, $to);
        $print = true;

        if ($format == 'pdf') {
            $pdf = PDF::loadView('reports.attendance_print', compact('schedules', 'from', 'to', 'print'))
                ->setPaper('a4', 'portrait');

            return $pdf->stream('Attendance Report.pdf');
        } else {
            $rows = [];

            foreach ($schedules as $schedule) {
                $rows[] = [
                    date('l, jS M, Y', strtotime($schedule->day)),
                    $schedule->course->course_code . ' ' . $schedule->course->name,
                    $schedule->class_course->size,
                    $schedule->attendance
                ];
            }

            $data = [
                'headers' => [
                    'Date',
                    'Exam',
                    'Expected Attendance',
                    'Actual Attendance'
                ],
                'data' => $rows
            ];

            $file_name = 'Examination Attendance Report';
            $title = 'Attendance report from ' . date('l, jS M, Y', strtotime($from)) . ' to ' . date('l, jS M, Y', strtotime($to));

            $excel_service = new ExcelExportService($file_name, $data, $title);

            return $excel_service->downloadExcel();
        }
    }

    public function exportInvigilatorsReport(Request $request)
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
            'professor_id' => 'required'
        ];

        $this->validate($request, $rules);

        $invigilator = Professor::find($request->professor_id);
        $format = $request->format;

        if (!$invigilator) {
            return response()->json(['message' => 'This professor does not exist'], 404);
        }

        $from = Carbon::parse($request->start_date)->toDateString();
        $to = Carbon::parse($request->end_date)->toDateString();

        $schedules = $this->getSchedulesForProfessor($from, $to, $request->professor_id);
        $print = true;

        if ($format == 'pdf') {
            $pdf = PDF::loadView('reports.invigilators_print', compact('schedules', 'from', 'to', 'invigilator', 'print'))
                ->setPaper('a4', 'portrait');

            return $pdf->stream('Invigilators Report.pdf');
        } else {
            $rows = [];

            foreach ($schedules as $schedule) {
                $rows[] = [
                    date('l, jS M, Y', strtotime($schedule->day)),
                    $schedule->course->course_code . ' ' . $schedule->course->name,
                    $schedule->class_course->size,
                    implode("/", $schedule->rooms()->pluck('name')->toArray())
                ];
            }

            $data = [
                'headers' => [
                    'Date',
                    'Exam',
                    'Venues',
                ],
                'data' => $rows
            ];

            $file_name = 'Examination Invigilators Report for ' . $invigilator->name;
            $title = 'Examination Invigilators report from ' . date('l, jS M, Y', strtotime($from)) . ' to ' . date('l, jS M, Y', strtotime($to)) . ' for ' . $invigilator->name;

            $excel_service = new ExcelExportService($file_name, $data, $title);

            return $excel_service->downloadExcel();
        }
    }

    public function exportRoomsReport(Request $request)
    {
        $rules = [
            'start_date' => 'required',
            'end_date' => 'required',
            'room_id' => 'required'
        ];

        $this->validate($request, $rules);

        $room = Room::find($request->room_id);

        if (!$room) {
            return response()->json(['message' => 'This room does not exist'], 404);
        }

        $from = Carbon::parse($request->start_date)->toDateString();
        $to = Carbon::parse($request->end_date)->toDateString();
        $format = $request->format;

        $schedules = $this->getSchedulesForRoom($from, $to, $request->room_id);
        $print = true;

        if ($format == 'pdf') {
            $pdf = PDF::loadView('reports.rooms_print', compact('schedules', 'from', 'to', 'room', 'print'))
                ->setPaper('a4', 'portrait');

            return $pdf->stream('Exam Venues Report.pdf');
        } else {
            $rows = [];

            foreach ($schedules as $schedule) {
                $rows[] = [
                    date('l, jS M, Y', strtotime($schedule->day)),
                    $schedule->course->course_code . ' ' . $schedule->course->name,
                    implode("/", $schedule->invigilators()->pluck('name')->toArray())
                ];
            }

            $data = [
                'headers' => [
                    'Date',
                    'Exam',
                    'Invigilators',
                ],
                'data' => $rows
            ];

            $file_name = 'Examination Venues Report for ' . $room->name;
            $title = 'Examination Venues report from ' . date('l, jS M, Y', strtotime($from)) . ' to ' . date('l, jS M, Y', strtotime($to)) . ' for ' . $room->name;

            $excel_service = new ExcelExportService($file_name, $data, $title);

            return $excel_service->downloadExcel();
        }
    }

    public function exportLecturersReport(Request $request)
    {
        $rules = [
            'professor_id' => 'required'
        ];

        $this->validate($request, $rules);

        $schedules = $this->getLecturerSchedules($request->professor_id);
        $lecturer = Professor::find($request->professor_id);
        $format = $request->format;
        $print = true;

        if (!$lecturer) {
            return response()->json(['message' => 'This lecturer does not exist'], 404);
        }

        if ($format == 'pdf') {
            $pdf = PDF::loadView('reports.lecturers_print', compact('schedules', 'lecturer', 'print'))
                ->setPaper('a4', 'portrait');

            return $pdf->stream('Exam Venues Report.pdf');
        } else {
            $rows = [];

            foreach ($schedules as $day => $daySchedules) {
                $times = array_keys($daySchedules);
                natcasesort($times);

                $rows[] = [];
                $rows[] = [$day];

                foreach ($times as $time) {
                    foreach ($daySchedules[$time] as $schedule) {
                        $rows[] = [
                            $time,
                            $schedule->course->course_code . ' ' . $schedule->course->name,
                            $schedule->class->name,
                            $schedule->rooms()->first()->name
                        ];
                    }
                }
            }

            $data = [
                'headers' => [
                    'Time',
                    'Course',
                    'Class',
                    'Venue'
                ],
                'data' => $rows
            ];

            $file_name = 'Lecturer Schedule Report for ' . $lecturer->name;
            $title = 'Lecturer schedule report for ' . $lecturer->name;

            $excel_service = new ExcelExportService($file_name, $data, $title);

            return $excel_service->downloadExcel();
        }
    }

    private function getIncidents($from, $to)
    {
        return ExamIncident::with('schedule')
            ->whereHas('schedule', function($query) use ($from, $to) {
                $query->whereDate('created_at', '>=', $from)
                    ->whereDate('created_at', '<=', $to);
            })
            ->get();
    }

    private function getSchedules($from, $to)
    {
        return TimetableSchedule::with('course')
            ->forExams()
            ->whereDate('day', '>=', $from)
            ->whereDate('day', '<=', $to)
            ->get();
    }

    private function getSchedulesForProfessor($from, $to, $professor_id)
    {
        return TimetableSchedule::with('course')
            ->forExams()
            ->whereDate('day', '>=', $from)
            ->whereDate('day', '<=', $to)
            ->whereHas('invigilators', function($query) use ($professor_id) {
                $query->where('professors.id', $professor_id);
            })
            ->get();
    }

    private function getSchedulesForRoom($from, $to, $room_id)
    {
        return TimetableSchedule::with('course')
            ->forExams()
            ->whereDate('day', '>=', $from)
            ->whereDate('day', '<=', $to)
            ->whereHas('rooms', function($query) use ($room_id) {
                $query->where('rooms.id', $room_id);
            })
            ->get();
    }

    private function getLecturerSchedules($professor_id)
    {
        $data = [];

        $schedules = TimetableSchedule::with('course')
            ->forLectures()
            ->get()
            ->filter(function ($schedule) use ($professor_id) {
                $professor_ids = $schedule->class_course->professors()->pluck('professors.id')->toArray();

                return in_array($professor_id, $professor_ids);
            });

        foreach ($schedules as $schedule) {
            if (!isset($data[$schedule->day][$schedule->timeslot->label])) {
                $data[$schedule->day][$schedule->timeslot->label] = [];
            }

            $data[$schedule->day][$schedule->timeslot->label][] = $schedule;
        }

        ksort($data);

        logger($data);

        return $data;
    }
}
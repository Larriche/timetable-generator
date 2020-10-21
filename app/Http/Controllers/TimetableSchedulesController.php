<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use App\Models\TimetableSchedule;
use App\Services\TimetableScheduleService;

class TimetableSchedulesController extends Controller
{
    /**
     * Create a new instance of this controller
     *
     * @param \App\Services\TimetableScheduleService $service This controller's service class
     */
    public function __construct(TimetableScheduleService $service)
    {
        $this->service = $service;
    }

    /**
     * Get a timetable schedule by id
     *
     * @param \Illuminate\Http\Request The HTTP request
     * @param int $id Id of timetable schedule
     */
    public function show(Request $request, $id)
    {
        $schedule = $this->service->show($id);

        if ($schedule) {
            return Response::json($schedule, 200);
        } else {
            return Response::json(['errors' => ['Schedule not found']], 404);
        }
    }

    /**
     * Update attendance for a timetable scheduling
     *
     * @param \Illuminate\Http\Request $request The HTTP request
     * @return \Illuminate\Http\Response The HTTP response
     */
    public function updateAttendance(Request $request)
    {
        $rules = [
            'attendance' => 'required|numeric',
            'schedule_id' => 'required|exists:timetable_schedules,id'
        ];

        $this->validate($request, $rules);

        $schedule = TimetableSchedule::find($request->schedule_id);

        if ($schedule) {
            $schedule->update([
                'attendance' => $request->attendance
            ]);

            return response()->json(['message' => 'Attendance information has been updated'], 200);
        }

        return response()->json(['error' => 'A system error occurred'], 500);
    }
}
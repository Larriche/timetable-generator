<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use App\Services\ExamIncidentsService;

class ExamIncidentsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ExamIncidentsService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    /**
     * Save an incident
     *
     * @param \Illuminate\Http\Request $request The HTTP request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'description' => 'required',
            'schedule_id' => 'required|exists:timetable_schedules,id'
        ];

        $this->validate($request, $rules);

        $incident = $this->service->store($request->all());

        if ($incident) {
            return Response::json(['message' => 'Incident has been recorded'], 200);
        } else {
            return Response::json(['error' => 'An unknown system error occurred'], 500);
        }
    }
}

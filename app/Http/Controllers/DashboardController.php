<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;
use App\Services\TimetableService;

use App\Models\Day;
use App\Models\Timetable;
use App\Models\AcademicPeriod;

class DashboardController extends Controller
{
    /**
     * Create a new instance of this controller
     *
     */
    public function __construct(DashboardService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
        $this->middleware('activated');
    }

    /**
     * Show the application's dashboard
     */
    public function index(Request $request)
    {
        $data = $this->service->getData();
        $timetables = Timetable::orderBy('created_at', 'DESC')->paginate(20);
        $academicPeriods = AcademicPeriod::all();
        $days = TimetableService::DAYS;

        if ($request->ajax()) {
            return view('dashboard.timetables', compact('timetables'));
        }

        return view('dashboard.index', compact('data', 'timetables', 'academicPeriods', 'days'));
    }
}

<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;

use App\Models\Room;
use App\Models\Block;
use App\Services\RoomsService;

class RoomsController extends Controller
{
    /**
     * Service helper class for this controller
     *
     * @var App\Services\RoomsService
     */
    protected $service;

    /**
     * Create a room controller instance
     *
     * @param App\Services\RoomsService $service Service class for this controller
     */
    public function __construct(RoomsService $service)
    {
        $this->middleware('auth');
        $this->middleware('activated');
        $this->service = $service;
    }

    /**
     * Get a listing of rooms
     *
     * @param Illuminate\Http\Request $request The HTTP request
     * @param Illuminate\Http\Response The HTTP response
     */
    public function index(Request $request)
    {
        $data = [
            'keyword' => $request->has('keyword') ? $request->keyword : null,
            'order_by' => 'name',
            'paginate' => 'true',
            'per_page' => 20,
            'select_form' => $request->has('select_form'),
        ];

        if ($request->has('block_id')) {
            $data['block_id'] = $request->block_id;
        }

        $rooms = $this->service->all($data);

        $blocks = Block::all();

        if ($request->ajax()) {
            if ($request->has('select_view')) {
                return view('rooms.select_form', compact('rooms'));
            }

            return view('rooms.table', compact('rooms'));
        }

        return view('rooms.index', compact('rooms', 'blocks'));
    }

    /**
     * Add a new room to the database
     *
     * @param Illuminate\Http\Request $request The HTTP request
     * @param Illuminate\Http\Response The HTTP response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|unique:rooms,name',
            'capacity' => 'required|numeric',
            'block_id' => 'required|exists:blocks,id'
        ];

        $messages = [
            'name.unique' => 'This room already exists',
            'block_id.required' => 'Please select a building',
            'capacity.required' => 'Please enter seating capacity of room'
        ];

        $this->validate($request, $rules, $messages);

        $room = $this->service->store($request->all());

        if ($room) {
            return Response::json(['message' => 'Room added'], 200);
        } else {
            return Response::json(['error' => 'A system error occurred'], 500);
        }
    }

    /**
     * Get a room by id
     *
     * @param int id The id of the room
     * @param Illuminate\Http\Request $request HTTP request
     */
    public function show($id, Request $request)
    {
        $room = $this->service->show($id);

        if ($room) {
            return Response::json($room, 200);
        } else {
            return Response::json(['error' => 'Room not found'], 404);
        }
    }

    /**
     * Update room with given ID
     *
     * @param int id The id of the room to be updated
     * @param Illuminate\Http\Request The HTTP request
     */
    public function update($id, Request $request)
    {
        $rules = [
            'name' => 'required|unique:rooms,name,' . $id,
            'capacity' => 'required|numeric',
            'block_id' => 'required|exists:blocks,id'
        ];

        $messages = [
            'name.unique' => 'This room already exists',
            'block_id.required' => 'Please select a building',
            'capacity.required' => 'Please enter seating capacity of room'
        ];

        $this->validate($request, $rules, $messages);

        $room = $this->service->show($id);

        if (!$room) {
            return Response::json(['error' => 'Room not found'], 404);
        }

        $room = $this->service->update($id, $request->all());

        return Response::json(['message' => 'Room updated'], 200);
    }

    public function destroy($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return Response::json(['error' => 'Room not found'], 404);
        }

        if ($this->service->delete($id)) {
            return Response::json(['message' => 'Room has been deleted'], 200);
        } else {
            return Response::json(['error' => 'An unknown system error occurred'], 500);
        }
    }
}

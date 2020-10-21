<?php

namespace App\Http\Controllers;

use Response;
use App\Models\Block;
use Illuminate\Http\Request;
use App\Services\BlocksService;

class BlocksController extends Controller
{
    /**
     * Service class for handling operations relating to this
     * controller
     *
     * @var App\Services\BlocksService $service
     */
    protected $service;

    /**
     * Create a new instance of this controller
     *
     * @param App\Services\BlocksService $service This controller's service class
     */
    public function __construct(BlocksService $service)
    {
        $this->service = $service;
        $this->middleware('auth');
        $this->middleware('activated');
    }

    /**
     * Show landing page for blocks module
     *
     * @param Illuminate\Http\Request $request The HTTP request
     */
    public function index(Request $request)
    {
        $blocks = $this->service->all([
            'keyword' => $request->has('keyword') ? $request->keyword : null,
            'order_by' => 'name',
            'paginate' => 'true',
            'per_page' => 20
        ]);

        if ($request->ajax()) {
            return view('blocks.table', compact('blocks'));
        }

        return view('blocks.index', compact('blocks'));
    }

    /**
     * Add a new professor to the database
     *
     * @param Illuminate\Http\Request The HTTP request
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required'
        ];

        $this->validate($request, $rules);

        $block = $this->service->store($request->all());

        if ($block) {
            return Response::json(['message' => 'Building added'], 200);
        } else {
            return Response::json(['error' => 'An unknown system error occurred'], 500);
        }
    }

    /**
     * Get and return data about a building
     *
     * @param int $id Id of building
     * @return Illuminate\Http\Response The data as a JSON response
     */
    public function show($id)
    {
        $block = $this->service->show($id);

        if ($block) {
            return Response::json($block, 200);
        } else {
            return Response::json(['errors' => ['Building not found']], 404);
        }
    }

    /**
     * Update the professor with the given id
     *
     * @param int $id Id of the block
     * @param Illuminate\Http\Request $request The HTTP request
     */
    public function update($id, Request $request)
    {
        $block = Block::find($id);

        if (!$block) {
            return Response::json(['errors' => ['Building does not exist']], 404);
        }

        $rules = [
            'name' => 'required',
        ];

        $this->validate($request, $rules);

        $block = $this->service->update($id, $request->all());

        return Response::json(['message' => 'Building updated'], 200);
    }


    /**
     * Delete the professor with the given id
     *
     * @param int $id Id of professor to delete
     */
    public function destroy($id)
    {
        $block = Block::find($id);

        if (!$block) {
            return Response::json(['error' => 'Building not found'], 404);
        }

        if ($this->service->delete($id)) {
            return Response::json(['message' => 'Building has been deleted'], 200);
        } else {
            return Response::json(['error' => 'An unknown system error occurred'], 500);
        }
    }
}

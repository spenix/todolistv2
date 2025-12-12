<?php

namespace App\Http\Controllers;

use App\Models\Todolist;
use App\Http\Requests\{StoreTodolistRequest, UpdateTodolistRequest};
use App\Repositories\TodolistRepositoryInterface;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Illuminate\Http\{RedirectResponse, Request};
use Inertia\Inertia;


class TodolistController extends Controller
{
    protected $todoListRepo;

    public function __construct(TodolistRepositoryInterface $todoListRepo) {
        $this->todoListRepo = $todoListRepo;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tz = Config::get('app.timezone', 'UTC');
        $today = Carbon::now($tz)->toDateString();

        $formattedDates = $this->todoListRepo->getTodoDates();

        return Inertia::render('Todolist/Index', [
            'todoDates' => $formattedDates,
            'dateToday' => $today,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodolistRequest $request) : RedirectResponse
    {
        try {

            $this->todoListRepo->create($request->all());

            return redirect()->back();
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Todolist $todolist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Todolist $todolist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodolistRequest $request, $id)
    {
        try {
            $this->todoListRepo->update($id, $request->all());
            return redirect()->back();
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
       try {
            $this->todoListRepo->delete($id);
            return redirect()->back();
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }


    public function get_todos(Request $request)
{
    try {
        $query = Todolist::query();
        // Search by keyword (partial match on description)
        if ($request->has('search') && $request->search !== '') {

            $query->where('description', 'LIKE', '%' . $request->search . '%');
        }

        // Optional: filter by date
        if ($request->has('date') && ($request->date !== '' && $request->date !== 'All')) {
            $query->where('task_date', $request->date);
        }

        if ($request->has('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }

        $data = $query->orderBy('task_date', 'desc')->get();

        return response()->json($data);

    } catch (\Throwable $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage(),
        ], 500);
    }
}
}

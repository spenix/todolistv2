<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Todolist;
use Illuminate\Support\Facades\Config;

class TodolistRepository implements TodolistRepositoryInterface
{
    public function getAll()
    {
        return Todolist::all();
    }

    public function findById($id)
    {
        return Todolist::find($id);
    }

    public function create(array $data)
    {
        $payload = [
                'description' => $data['description'],
                'task_date' => $data['task_date'],
                'priority' => $data['priority'],
                'task_date' => $data['task_date'],
            ];

            $isExist = Todolist::where( [
                'description' => $data['description'],
                'task_date' => $data['task_date'],
            ])->count();

            if ($isExist) {
                return back()->withErrors([
                    'errorMessage' => 'The task already exists.',
                ]);
            }

        return Todolist::create($payload);
    }

    public function update($id, array $data)
    {
        $payload = [
            'description' => $data['description'],
            'task_date' => $data['task_date'],
            'priority' => $data['priority'],
            'task_date' => $data['task_date'],
        ];

         $isExist = Todolist::where( [
            'description' => $data['description'],
            'task_date' => $data['task_date'],
        ])->where('id', '!=', $id)->count();
        if ($isExist) {
            return back()->withErrors([
                'errorMessage' => 'The task has already been created.',
            ]);
        }
        
        $model = Todolist::find($id);

        if ($model) {
            $model->update($payload);
            return $model;
        }
        return null;
    }

    public function delete($id)
    {
        $model = Todolist::find($id);
        if ($model) {
            return $model->delete();
        }
        return false;
    }

    public function getTodoDates()
    {
        $tz = Config::get('app.timezone', 'UTC');
        $dates = Todolist::distinct()
            ->orderBy('task_date', 'desc')
            ->pluck('task_date');

        $formattedDates = $dates->map(function($date) use ($tz) {
            $carbonDate = Carbon::parse($date, $tz);

            if ($carbonDate->isToday()) {
                return ['value' => $date, 'label' => 'Today'];
            } elseif ($carbonDate->isYesterday()) {
                return ['value' => $date, 'label' => 'Yesterday'];
            } else {
                return ['value' => $date, 'label' => $carbonDate->format('l, F d, Y')];
            }
        });

        return array_merge([['value' => 'All', 'label' => 'All']], $formattedDates->toArray());
    }
}

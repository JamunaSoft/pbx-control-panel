<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TimeCondition;
use Illuminate\Http\Request;

class TimeConditionController extends Controller
{
    /**
     * Display a listing of time conditions.
     */
    public function index(Request $request)
    {
        $query = TimeCondition::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by enabled
        if ($request->has('enabled')) {
            $query->where('enabled', $request->boolean('enabled'));
        }

        $timeConditions = $query->orderBy('name')->paginate(25);

        return view('time-conditions.index', compact('timeConditions'));
    }

    /**
     * Show the form for creating a new time condition.
     */
    public function create()
    {
        return view('time-conditions.create');
    }

    /**
     * Store a newly created time condition.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:time_conditions',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'integer|between:0,6',
            'days_of_month' => 'nullable|array',
            'days_of_month.*' => 'integer|between:1,31',
            'months' => 'nullable|array',
            'months.*' => 'integer|between:1,12',
            'year' => 'nullable|integer|min:2024|max:2050',
            'holidays_enabled' => 'boolean',
            'holiday_dates' => 'nullable|array',
            'holiday_dates.*' => 'date_format:m-d',
            'timezone' => 'required|string',
            'destination_true' => 'nullable|string|max:255',
            'destination_false' => 'nullable|string|max:255',
            'enabled' => 'boolean',
        ]);

        TimeCondition::create($validated);

        return redirect()->route('time-conditions.index')->with('success', 'Time condition created successfully');
    }

    /**
     * Display the specified time condition.
     */
    public function show(TimeCondition $timeCondition)
    {
        return view('time-conditions.show', compact('timeCondition'));
    }

    /**
     * Show the form for editing the time condition.
     */
    public function edit(TimeCondition $timeCondition)
    {
        return view('time-conditions.edit', compact('timeCondition'));
    }

    /**
     * Update the time condition.
     */
    public function update(Request $request, TimeCondition $timeCondition)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:time_conditions,name,' . $timeCondition->id,
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'integer|between:0,6',
            'days_of_month' => 'nullable|array',
            'days_of_month.*' => 'integer|between:1,31',
            'months' => 'nullable|array',
            'months.*' => 'integer|between:1,12',
            'year' => 'nullable|integer|min:2024|max:2050',
            'holidays_enabled' => 'boolean',
            'holiday_dates' => 'nullable|array',
            'holiday_dates.*' => 'date_format:m-d',
            'timezone' => 'required|string',
            'destination_true' => 'nullable|string|max:255',
            'destination_false' => 'nullable|string|max:255',
            'enabled' => 'boolean',
        ]);

        $timeCondition->update($validated);

        return redirect()->route('time-conditions.index')->with('success', 'Time condition updated successfully');
    }

    /**
     * Delete the time condition.
     */
    public function destroy(TimeCondition $timeCondition)
    {
        $timeCondition->delete();

        return redirect()->route('time-conditions.index')->with('success', 'Time condition deleted successfully');
    }
}

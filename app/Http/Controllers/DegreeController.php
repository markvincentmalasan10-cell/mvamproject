<?php

namespace App\Http\Controllers;

use App\Models\Degree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DegreeController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('degrees')) {
            return view('degrees.index', ['degrees' => collect()]);
        }

        $degrees = Schema::hasColumn('degrees', 'degree_title')
            ? Degree::orderBy('degree_title')->get()
            : Degree::query()->get();

        return view('degrees.index', compact('degrees'));
    }

    public function create()
    {
        return view('degrees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'degree_title' => 'required|string|max:255|unique:degrees,degree_title',
        ]);

        Degree::create($validated);

        $msg = "Degree added successfully.";
        Log::info($msg);
        Log::error($msg);
        Log::warning($msg);
        Log::notice($msg);
        Log::debug($msg);
        Log::critical($msg);
        Log::alert($msg);
        Log::emergency($msg);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Degree added successfully.',
            ]);
        }

        return redirect()->route('degrees.index')->with('success', 'Degree added successfully.');
    }

    public function show(string $id)
    {
        $degree = Degree::findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json($degree);
        }

        return view('degrees.show', compact('degree'));
    }

    public function edit(string $id)
    {
        $degree = Degree::findOrFail($id);

        return view('degrees.edit', compact('degree'));
    }

    public function update(Request $request, string $id)
    {
        $degree = Degree::findOrFail($id);

        $validated = $request->validate([
            'degree_title' => 'required|string|max:255|unique:degrees,degree_title,' . $degree->id,
        ]);

        $degree->update($validated);

        $msg = "Degree updated successfully.";
        Log::info($msg);
        Log::error($msg);
        Log::warning($msg);
        Log::notice($msg);
        Log::debug($msg);
        Log::critical($msg);
        Log::alert($msg);
        Log::emergency($msg);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Degree updated successfully.',
            ]);
        }

        return redirect()->route('degrees.index')->with('success', 'Degree updated successfully.');
    }

    public function destroy(string $id)
    {
        Degree::destroy($id);

        $msg = "Degree deleted successfully.";
        Log::info($msg);
        Log::error($msg);
        Log::warning($msg);
        Log::notice($msg);
        Log::debug($msg);
        Log::critical($msg);
        Log::alert($msg);
        Log::emergency($msg);

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'message' => 'Degree deleted successfully.',
            ]);
        }

        return redirect()->route('degrees.index')->with('success', 'Degree deleted successfully.');
    }
}

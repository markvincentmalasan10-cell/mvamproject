<?php

namespace App\Http\Controllers;

use App\Models\Degree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DegreeController extends Controller
{
    public function index()
    {
        try {
            $this->ensureDegreeSchema();
            $degrees = $this->degreeQuery()->get();
        } catch (Throwable $exception) {
            Log::error('Unable to load degree list.', [
                'message' => $exception->getMessage(),
            ]);

            $degrees = collect();
        }

        return view('degrees.index', compact('degrees'));
    }

    public function create()
    {
        $this->ensureDegreeSchema();

        return view('degrees.create');
    }

    public function store(Request $request)
    {
        try {
            $this->ensureDegreeSchema();

            $validated = $request->validate($this->degreeValidationRules());

            Degree::create($this->degreeDataForExistingColumns($validated));
        } catch (Throwable $exception) {
            Log::error('Unable to store degree.', [
                'message' => $exception->getMessage(),
            ]);

            $message = str_contains(strtolower($exception->getMessage()), 'unique')
                ? 'Degree already exists. Please use a different title.'
                : 'Degree could not be saved. Please check the title and try again.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $message,
                    'errors' => [
                        'degree_title' => [$message],
                    ],
                ], 422);
            }

            return back()->withErrors(['degree_title' => $message])->withInput();
        }

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
        $this->ensureDegreeSchema();

        $degree = Degree::findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json($degree);
        }

        return view('degrees.show', compact('degree'));
    }

    public function edit(string $id)
    {
        $this->ensureDegreeSchema();

        $degree = Degree::findOrFail($id);

        return view('degrees.edit', compact('degree'));
    }

    public function update(Request $request, string $id)
    {
        try {
            $this->ensureDegreeSchema();

            $degree = Degree::findOrFail($id);

            $validated = $request->validate($this->degreeValidationRules($degree->id));

            $degree->update($this->degreeDataForExistingColumns($validated));
        } catch (Throwable $exception) {
            Log::error('Unable to update degree.', [
                'degree_id' => $id,
                'message' => $exception->getMessage(),
            ]);

            $message = str_contains(strtolower($exception->getMessage()), 'unique')
                ? 'Degree already exists. Please use a different title.'
                : 'Degree could not be updated. Please check the title and try again.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => $message,
                    'errors' => [
                        'degree_title' => [$message],
                    ],
                ], 422);
            }

            return back()->withErrors(['degree_title' => $message])->withInput();
        }

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
        $this->ensureDegreeSchema();

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

    private function ensureDegreeSchema(): void
    {
        try {
            Artisan::call('app:repair-schema');
        } catch (Throwable $exception) {
            Log::error('Unable to repair degree schema.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }

    private function degreeQuery()
    {
        if (! Schema::hasTable('degrees')) {
            return Degree::query()->whereRaw('1 = 0');
        }

        $query = Degree::query();

        if (Schema::hasColumn('degrees', 'degree_title')) {
            $query->orderBy('degree_title');
        }

        return $query;
    }

    private function degreeValidationRules(?int $ignoreId = null): array
    {
        $rule = 'required|string|max:255';

        if (Schema::hasTable('degrees') && Schema::hasColumn('degrees', 'degree_title')) {
            $rule .= '|unique:degrees,degree_title';

            if ($ignoreId) {
                $rule .= ',' . $ignoreId;
            }
        }

        return [
            'degree_title' => $rule,
        ];
    }

    private function degreeDataForExistingColumns(array $data): array
    {
        return collect($data)
            ->filter(fn ($value, $column) => Schema::hasColumn('degrees', $column))
            ->all();
    }
}

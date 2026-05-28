<?php

namespace App\Http\Controllers;
use App\Models\Degree;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\UserAccount;
use Throwable;

class StudentController extends Controller
{




     // Method 1: associative array

//     public function greet() {
    
//     $name = "Juan Dela Cruz";
//     return view("greeting", ['name'=> $name]);
// }

    //Method 2: using Compact

//     public function greet() {
    
//     $name = "Juan Dela Cruz";
//     return view("greeting", compact ('name'));
// }

    //Method 3 : passing multiple data

//     public function greet() {
    
//     $name = "Juan Dela Cruz";
//     $address = "San Carlos";
//     return view("greeting", compact ('name', 'address'));
// }

    public function displayProfile() {
        $student = null;

        if (session('user_role') === 'student' && session('student_id')) {
            $student = Student::with('degree')->find(session('student_id'));
        }

        return view('profile', compact('student'));

}

    public function displayDashboard() {
    
    $user = session('logged_user', session('student_name'));
    
    return view('dashboard')->with("user", $user);
}

    public function displayAboutUs() {
    
    
    return view('aboutUs');
}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
    // $grade = 70;
    

    // $students = array(
    //     array("name"=>"Trixia","course"=>"BSIT","age"=>"20"),
    //     array("name"=>"Vincent","course"=>"BSIT","age"=>"21"),
    //     array("name"=>"Neriss","course"=>"BSIT" , "age"=>"22"),
    //     array("name"=>"Neriss","course"=>"BSIT" , "age"=>"19"),
    //     array("name"=>"Neriss","course"=>"BSIT" , "age"=>"10"),
    // );

    // return view("student")
    //     ->with("students",$students)
    //     ->with("grade",$grade);

        try {
            if (! Schema::hasTable('students')) {
                $students = new LengthAwarePaginator([], 0, 5);
                $user = session('logged_user', session('student_name'));

                return view('student')->with("students", $students)->with("user", $user);
            }

            $query = Student::query();

            if (Schema::hasTable('degrees') && Schema::hasColumn('students', 'degree_id')) {
                $query->with('degree');
            }

            $students = $query->paginate(5);
        } catch (Throwable $exception) {
            Log::error('Unable to load student list.', [
                'message' => $exception->getMessage(),
            ]);

            $students = new LengthAwarePaginator([], 0, 5);
            $user = session('logged_user', session('student_name'));

            return view('student')->with("students", $students)->with("user", $user);
        }

        if ($request->ajax() && ! $request->boolean('full_page')) {
            return view('vendor.pagination.studentList')->with('students', $students);
        }

       $user = session('logged_user', session('student_name'));

        return view('student')->with("students", $students)->with("user", $user);
    }

    public function exportPdf()
    {
        $students = $this->studentReportStudents();

        $data = [
            'title' => 'All Students Report',
            'date' => now()->format('F d, Y h:i A'),
            'students' => $students,
        ];

        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return back()->with('error', 'PDF package is not installed yet. Run: composer require barryvdh/laravel-dompdf');
        }

        return \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report', $data)
            ->setPaper('a4', 'portrait')
            ->download('all-students-report.pdf');
    }

    public function exportExcel()
    {
        $students = $this->studentReportStudents();

        return response()
            ->view('excel.students', [
                'students' => $students,
                'date' => now()->format('F d, Y h:i A'),
            ])
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="all-students.xls"');
    }

       
        
    
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $degrees = Schema::hasTable('degrees') && Schema::hasColumn('degrees', 'degree_title')
            ? Degree::orderBy('degree_title')->get()
            : (Schema::hasTable('degrees') ? Degree::query()->get() : collect());
         return view('add_student', compact('degrees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contactno' => 'required|string|max:255',
            'degree_id' => 'nullable|exists:degrees,id',
            'username' => 'required|string|max:255|unique:user_account,username',
            'password' => 'required|string|min:6',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $storedPath = $request->file('image')->store('student_images', 'public');
            $imagePath = 'storage/' . $storedPath;
        }

        DB::transaction(function () use ($validated, $imagePath) {
            $userAccount = UserAccount::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'student',
                'is_active' => true,
                'must_change_password' => false,
                'is_first_login' => true,
            ]);

            Student::create([
                'fname' => $validated['fname'],
                'mname' => $validated['mname'] ?? null,
                'lname' => $validated['lname'],
                'email' => $validated['email'],
                'contactno' => $validated['contactno'],
                'degree_id' => $validated['degree_id'] ?? null,
                'user_account_id' => $userAccount->id,
                'image_path' => $imagePath,
            ]);
        });

        $msg = "Student added successfully.";
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
                'message' => 'Student added successfully.',
            ]);
        }

        return redirect('/student')->with('success', 'Student added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $student = Student::with(['degree', 'userAccount'])->findOrFail($id);

        if ($request->expectsJson()) {
            return response()->json($student);
        }

        return view('showStudentdetail', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::findOrFail($id);
        $degrees = Schema::hasTable('degrees') && Schema::hasColumn('degrees', 'degree_title')
            ? Degree::orderBy('degree_title')->get()
            : (Schema::hasTable('degrees') ? Degree::query()->get() : collect());



        return view('edit_student', compact('student', 'degrees'));


    }

    private function studentReportStudents()
    {
        if (! Schema::hasTable('students')) {
            return collect();
        }

        $relations = [];

        if (Schema::hasTable('degrees') && Schema::hasColumn('students', 'degree_id')) {
            $relations[] = 'degree';
        }

        if (Schema::hasTable('user_account') && Schema::hasColumn('students', 'user_account_id')) {
            $relations[] = 'userAccount';
        }

        $query = Student::query()->with($relations);

        if (Schema::hasColumn('students', 'lname')) {
            $query->orderBy('lname');
        }

        if (Schema::hasColumn('students', 'fname')) {
            $query->orderBy('fname');
        }

        return $query->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       $validated = $request->validate([
            'fname' => 'required|string|max:255',
            'mname' => 'nullable|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'contactno' => 'required|string|max:255',
            'degree_id' => 'nullable|exists:degrees,id',
        ]);

       $student = Student::findOrFail($id);

        DB::transaction(function () use ($student, $validated) {
            $student->fname = $validated['fname'];
            $student->mname = $validated['mname'] ?? null;
            $student->lname = $validated['lname'];
            $student->email = $validated['email'];
            $student->contactno = $validated['contactno'];
            $student->degree_id = $validated['degree_id'] ?? null;
            $student->save();

            if ($student->userAccount) {
                $student->userAccount->update([
                    'email' => $validated['email'],
                ]);
            }
        });

        $msg = "Student updated successfully.";
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
                'message' => 'Student updated successfully.',
            ]);
        }

        return redirect('/student')->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);

        DB::transaction(function () use ($student) {
            $userAccount = $student->userAccount;
            $imagePath = $student->image_path;
            $student->delete();

            if ($userAccount && $userAccount->role === 'student') {
                $userAccount->delete();
            }

            if ($imagePath) {
                Storage::disk('public')->delete(str_replace('storage/', '', $imagePath));
            }
        });

        $msg = "Student deleted successfully.";
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
                'message' => 'Student deleted successfully.',
            ]);
        }

        return redirect('/student')->with('success', 'Student deleted successfully.');

    }
}

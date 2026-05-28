<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DegreeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PagesController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

Route::redirect('/', '/login');

Route::get('/login', function () {
    if (session()->has('user_account_id')) {
        return redirect('/dashboard');
    }

    return view('login');
});
Route::post('/login', [PagesController::class, 'login']);

Route::get('/change-password', [PagesController::class, 'showChangePassword']);
Route::post('/change-password', [PagesController::class, 'changePassword']);
Route::get('/sessionUserAccount', [PagesController::class, 'sessionUserAccount']);
Route::match(['get', 'post'], '/logout', [PagesController::class, 'logout']);
Route::get('/welcome-student', function () {
    return view('welcomeStudent');
});

Route::get('/maintenance', function () {
    return view('maintenance');
});

Route::get('/crud-sync', function () {
    $versions = [];

    foreach (['students', 'teachers', 'degrees'] as $table) {
        try {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $latest = Schema::hasColumn($table, 'updated_at')
                ? DB::table($table)->max('updated_at')
                : null;

            $count = DB::table($table)->count();

            $versions[$table] = [
                'action' => 'changed',
                'version' => $count . '-' . ($latest ?: '0'),
            ];
        } catch (Throwable $exception) {
            Log::error('Unable to read CRUD sync version.', [
                'table' => $table,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    return response()->json(['versions' => $versions]);
});

Route::get('/student_courses', [PagesController::class, 'studentCourses']);

Route::middleware('sessionUserMw')->group(function () {
    Route::get('/dashboard', [PagesController::class, 'dashboard']);
    Route::get('/demo', [PagesController::class, 'demo']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/students/export/pdf', [StudentController::class, 'exportPdf'])->name('students.export.pdf');
        Route::get('/students/export/excel', [StudentController::class, 'exportExcel'])->name('students.export.excel');
        Route::get('/students', [StudentController::class, 'index']);
        Route::resource('/student', StudentController::class);
        Route::resource('/students', StudentController::class);
        Route::resource('/teacher', TeacherController::class);
        Route::resource('/teachers', TeacherController::class);
        Route::resource('/users', UserController::class)->except(['create', 'show', 'edit']);
        Route::resource('/degrees', DegreeController::class);
    });

    Route::get('/user_profile', [PagesController::class, 'userProfile']);
    Route::get('/user_post', [PagesController::class, 'userPost']);

    Route::get('/profile', [StudentController::class,'displayProfile']);
    Route::get('/aboutus', [StudentController::class,'displayAboutUs']);
});

// Route::get ('/welcome', [PSUController::class,'welcome'])->name('Welcome');
// Route::get ('/mission', [PSUController::class,'mission'])->name('mission');
// Route::get ('/vision', [PSUController::class,'vision'])->name('vision');
// Route::get ('/EOMSPolicy', [PSUController::class,'EOMSPolicy'])->name('eomspolicy');

// Route::get ('/Student/{name}/{course}', [PSUController::class, 'Student'])->name('NAME');

// Route::get ('/Add', [CalculatorController::class,'Add']);
// Route::get ('/Subtract', [CalculatorController::class,'Subtract']);
// Route::get ('/Divide', [CalculatorController::class,'Divide']);
// Route::get ('/Multiply', [CalculatorController::class,'Multiply']);
// Route::get ('/Modulo', [CalculatorController::class,'Modulo']);


// function(){
//     // return "This is About Us Page";
//     $a =1;
//     $b = 2;
//     $sum = $a + $b;
//     return "Sum of the two num is $sum";
// }
// // Task 1: Creating Named Routes 

// Route::get ('/home', function(){
//     return "I am Melvin M. Agbuya. Welome to the Home page!";
// })->name('home.page');


// //Task 2: Using Named Routes 
// Route::get ('/redirect-home', function(){
//     return redirect()->route("home.page");
// })->name('home');

// //Task 3: Required Parameter

// Route::get('/greet/{name}', function ($name) {
//     return "hello, " . $name . "!";
// })->name("required parameter"); 

// //Task 4: Optional Parameter

// Route::get('/student/{name?}', function ($name = "display") {
//     return "Hello,  "."!" .$name ;
// })->name("optional parameter"); 

// //Task 5: route group with prefix
 
// Route::prefix("administrator")->group(
//     function () {
//         Route::get ('/dashboard', function(){
//             return "Dashboard";
//         })->name('redirectAdminDashboard');

//         Route::get ('/profile', function(){
//             return "Welcome to my profile";
//         })->name('adminProfileRoute');

//         Route::get ('/settings', function(){
//             return "Settings Page";
//         })->name('adminSettingPageRoute');
//     }
// ) ;

// //Task 6: redirect rout group

// Route::get ('/redirect-dashboard', function(){
//     return redirect()->route("redirectAdminDashboard");
// })->name('home');



// // Route::get ('/greet/{name?}/message/{msg}', function($name= 'guest', $msg= "hello world") {
// //     return "welcome ".$name." to laravel app development. $msg";
// // });

// // // Route::get ('/loginRoutes', function(){
// // //     return ("enter ur password");
// // // })->name('loginhome');

// // // Route::get ('/logoutRoutes', function(){
// // //     return redirect()->route("loginhome");
// // // })->name('logouthome');


// // // Route::get ('/users{id}', function($id){
// // //     return "user id:  ".$id ;
// // // })->where('id',[0-9]+ )->name('loginhome');

// // //student routes

// // Route::prefix("student")->group(
// //     function () {
// //         Route::get ('/profile', function(){
// //             return "this is profile page";
// //         })->name('studentProfileRoute');

// //         Route::get ('/dashboard', function(){
// //             return "this is dashboard page";
// //         })->name('studentDashboardRoute');

// //         Route::get ('/friendList', function(){
// //             return "this is frient list page";
// //         })->name('studentfriendListRoute');
// //     }
// // ) ;


// DEGREE ROUTES

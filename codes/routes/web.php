<?php

use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    $emails = [
        'test1@example.com', 'test2@example.com', 'test3@example.com',
        'test4@example.com', 'test5@example.com', 'test6@example.com',
        'test7@example.com', 'test8@example.com', 'test9@example.com',
        'test10@example.com', 'test11@example.com', 'test12@example.com',
        'test13@example.com', 'test14@example.com', 'test15@example.com',
        'test16@example.com', 'test17@example.com', 'test18@example.com',
        'test19@example.com', 'test20@example.com',
    ];

    for ($i =10; $i < 500; $i++) {
        $email = 'test'.$i.'@example.com';
        SendEmailJob::dispatch($email); // Dispatch one job per email
    }
    return 'echo "this is printed before all email has been sent";';
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

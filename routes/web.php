<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/clear-cache', function () {
    $exitCode1 = Artisan::call('route:clear');
    $exitCode2 = Artisan::call('config:clear');
    $exitCode3 = Artisan::call('view:clear');
    $exitCode4 = Artisan::call('cache:clear');
    // $exitCode5 = Artisan::call('optimize:clear');
    // return what you want
    return "Clear Success";
});


Route::get('phpmyinfo', function () {
    phpinfo();
})->name('phpmyinfo');

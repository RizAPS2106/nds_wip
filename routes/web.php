<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MarkerController;
<<<<<<< HEAD
use App\Http\Controllers\SpreadingController;
=======
use App\Http\Controllers\FormCutInputController;

>>>>>>> b00de9bf4019f45696331cb82332076853935d8d
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

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {
    // User
    Route::controller(UserController::class)->prefix("user")->group(function () {
        Route::put('/update/{id}', 'update')->name('update-user');
    });

    // Marker
    Route::controller(MarkerController::class)->prefix("marker")->group(function () {
        Route::get('/', 'index')->name('marker');
        Route::get('/create', 'create')->name('create-marker');
        Route::post('/store', 'store')->name('store-marker');
        Route::get('/edit', 'edit')->name('edit-marker');
        Route::put('/update', 'update')->name('update-marker');

        // get order
        Route::get('/get-order', 'getOrderInfo')->name('get-marker-order');
        // get colors
        Route::get('/get-colors', 'getColorList')->name('get-marker-colors');
        // get panels
        Route::get('/get-panels', 'getPanelList')->name('get-marker-panels');
        // get sizes
        Route::get('/get-sizes', 'getSizeList')->name('get-marker-sizes');
        // get count
        Route::get('/get-count', 'getCount')->name('get-marker-count');
        // get number
        Route::get('/get-number', 'getNumber')->name('get-marker-number');
    });

    // Spreading
    Route::controller(SpreadingController::class)->prefix("spreading")->group(function () {
        Route::get('/', 'index')->name('spreading');
        Route::get('/create', 'create')->name('create-spreading');
        Route::post('/getno_marker', 'getno_marker')->name('getno_marker');
        Route::get('/getdata_marker', 'getdata_marker')->name('getdata_marker');
        Route::get('/getdata_ratio', 'getdata_ratio')->name('getdata_ratio');
        Route::post('/store', 'store')->name('store-spreading');
    });
    // Form Cut Input
    Route::controller(FormCutInputController::class)->prefix("form-cut-input")->group(function () {
        Route::get('/', 'index')->name('form-cut-input');
        // Route::get('/create', 'create')->name('create-form-cut-input');
        // Route::post('/store', 'store')->name('store-form-cut-input');
        // Route::get('/edit', 'edit')->name('edit-form-cut-input');
        // Route::put('/update', 'update')->name('update-form-cut-input');
        Route::get('/process/{id}', 'process')->name('process-form-cut-input');
        Route::get('/get-number-data', 'getNumberData')->name('get-number-form-cut-input');
        Route::get('/get-scanned-item/{id}', 'getScannedItem')->name('get-scanned-form-cut-input');
        Route::put('/start-process/{id}', 'startProcess')->name('start-process-form-cut-input');
        Route::put('/next-process-one/{id}', 'nextProcessOne')->name('next-process-one-form-cut-input');
        Route::put('/next-process-two/{id}', 'nextProcessTwo')->name('next-process-two-form-cut-input');
        Route::get('/get-time-record/{noForm}', 'getTimeRecord')->name('get-time-form-cut-input');
        Route::post('/store-time-record', 'storeTimeRecord')->name('store-time-form-cut-input');
        Route::put('/finish-process/{id}', 'finishProcess')->name('finish-process-form-cut-input');

        // get order
        Route::get('/get-order', 'getOrderInfo')->name('get-marker-order');
        // get colors
        Route::get('/get-colors', 'getColorList')->name('get-marker-colors');
        // get panels
        Route::get('/get-panels', 'getPanelList')->name('get-marker-panels');
        // get sizes
        Route::get('/get-sizes', 'getSizeList')->name('get-marker-sizes');
        // get count
        Route::get('/get-count', 'getCount')->name('get-marker-count');
        // get number
        Route::get('/get-number', 'getNumber')->name('get-marker-number');
    });
});

Route::get('/', function () {
    return view('home', ['page' => 'home']);
})->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard', ['page' => 'dashboard']);
})->middleware('auth');

Route::get('/timer', function () {
    return view('example.timeout');
})->middleware('auth');

Route::get('/widgets', function () {
    return view('component.widgets');
})->middleware('auth');

Route::get('/kanban', function () {
    return view('component.kanban');
})->middleware('auth');

Route::get('/gallery', function () {
    return view('component.gallery');
})->middleware('auth');

Route::get('/calendar', function () {
    return view('component.calendar');
})->middleware('auth');

Route::get('/timeline', function () {
    return view('component.UI.timeline');
})->middleware('auth');

Route::get('/sliders', function () {
    return view('component.UI.sliders');
})->middleware('auth');

Route::get('/modals', function () {
    return view('component.UI.modals');
})->middleware('auth');

Route::get('/ribbons', function () {
    return view('component.UI.ribbons');
})->middleware('auth');

Route::get('/general', function () {
    return view('component.UI.general');
})->middleware('auth');

Route::get('/datatable', function () {
    return view('component.tables.data');
})->middleware('auth');

Route::get('/jsgrid', function () {
    return view('component.tables.jsgrid');
})->middleware('auth');

Route::get('/simpletable', function () {
    return view('component.tables.simple');
})->middleware('auth');

Route::get('/advanced-form', function () {
    return view('component.forms.advanced');
})->middleware('auth');

Route::get('/general-form', function () {
    return view('component.forms.general');
})->middleware('auth');

Route::get('/validation-form', function () {
    return view('component.forms.validation');
})->middleware('auth');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\SpreadingController;
use App\Http\Controllers\FormCutInputController;
use App\Http\Controllers\LapPemakaianController;
use App\Http\Controllers\StockerController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\MasterLokasiController;
use App\Http\Controllers\InMaterialController;
use App\Http\Controllers\OutMaterialController;
use App\Http\Controllers\MutLokasiController;
use App\Http\Controllers\QcPassController;

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

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    // User
    Route::controller(UserController::class)->prefix("user")->group(function () {
        Route::put('/update/{id?}', 'update')->name('update-user');
    });

    // Marker
    Route::controller(MarkerController::class)->prefix("marker")->middleware('marker')->group(function () {
        Route::get('/', 'index')->name('marker');
        Route::get('/create', 'create')->name('create-marker');
        Route::post('/store', 'store')->name('store-marker');
        Route::get('/edit', 'edit')->name('edit-marker');
        Route::put('/update', 'update')->name('update-marker');
        Route::post('/show', 'show')->name('show-marker');
        Route::post('/show_gramasi', 'show_gramasi')->name('show_gramasi');
        Route::post('/update_status', 'update_status')->name('update_status');
        Route::put('/update_marker', 'update_marker')->name('update_marker');

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
    Route::controller(SpreadingController::class)->prefix("spreading")->middleware('spreading')->group(function () {
        Route::get('/', 'index')->name('spreading');
        Route::get('/create', 'create')->name('create-spreading');
        Route::post('/getno_marker', 'getno_marker')->name('getno_marker');
        Route::get('/getdata_marker', 'getdata_marker')->name('getdata_marker');
        Route::get('/getdata_ratio', 'getdata_ratio')->name('getdata_ratio');
        Route::post('/store', 'store')->name('store-spreading');
        Route::put('/update', 'update')->name('update-spreading');
        Route::get('/get-order-info', 'getOrderInfo')->name('get-spreading-data');
        Route::get('/get-cut-qty', 'getCutQty')->name('get-cut-qty-data');
        // export excel
        // Route::get('/export_excel', 'export_excel')->name('export_excel');
        // Route::get('/export', 'export')->name('export');
    });

    // Form Cut Input
    Route::controller(FormCutInputController::class)->prefix("form-cut-input")->middleware("meja")->group(function () {
        Route::get('/', 'index')->name('form-cut-input');
        Route::get('/process/{id?}', 'process')->name('process-form-cut-input');
        Route::get('/get-number-data', 'getNumberData')->name('get-number-form-cut-input');
        Route::get('/get-scanned-item/{id?}', 'getScannedItem')->name('get-scanned-form-cut-input');
        Route::put('/start-process/{id?}', 'startProcess')->name('start-process-form-cut-input');
        Route::put('/next-process-one/{id?}', 'nextProcessOne')->name('next-process-one-form-cut-input');
        Route::put('/next-process-two/{id?}', 'nextProcessTwo')->name('next-process-two-form-cut-input');
        Route::get('/get-time-record/{noForm?}', 'getTimeRecord')->name('get-time-form-cut-input');
        Route::post('/store-scanned-item', 'storeScannedItem')->name('store-scanned-form-cut-input');
        Route::post('/store-time-record', 'storeTimeRecord')->name('store-time-form-cut-input');
        Route::post('/store-time-record-extension', 'storeTimeRecordExtension')->name('store-time-ext-form-cut-input');
        Route::post('/store-this-time-record', 'storeThisTimeRecord')->name('store-this-time-form-cut-input');
        Route::put('/finish-process/{id?}', 'finishProcess')->name('finish-process-form-cut-input');
        Route::get('/check-spreading-form/{noForm?}/{noMeja?}', 'checkSpreadingForm')->name('check-spreading-form-cut-input');
        Route::get('/check-time-record/{detailId?}', 'checkTimeRecordLap')->name('check-time-record-form-cut-input');
        Route::post('/store-lost-time/{id?}', 'storeLostTime')->name('store-lost-form-cut-input');
        Route::get('/check-lost-time/{id?}', 'checkLostTime')->name('check-lost-form-cut-input');
        Route::get('/get-form-cut-ratio', 'getRatio')->name('get-form-cut-ratio');

        // get order
        Route::get('/get-order', 'getOrderInfo')->name('form-cut-get-marker-order');
        // get colors
        Route::get('/get-colors', 'getColorList')->name('form-cut-get-marker-colors');
        // get panels
        Route::get('/get-panels', 'getPanelList')->name('form-cut-get-marker-panels');
        // get sizes
        Route::get('/get-sizes', 'getSizeList')->name('form-cut-get-marker-sizes');
        // get count
        Route::get('/get-count', 'getCount')->name('form-cut-get-marker-count');
        // get number
        Route::get('/get-number', 'getNumber')->name('form-cut-get-marker-number');
    });


    // Laporan
    Route::controller(LapPemakaianController::class)->prefix("lap_pemakaian")->middleware('admin')->group(function () {
        Route::get('/', 'index')->name('lap_pemakaian');
        // export excel
        Route::get('/export_excel', 'export_excel')->name('export_excel');
        Route::get('/export', 'export')->name('export');
    });

    // Stocker
    Route::controller(StockerController::class)->prefix("stocker")->middleware('stocker')->group(function () {
        Route::get('/', 'index')->name('stocker');
        Route::get('/show/{id?}', 'show')->name('show-stocker');
        Route::post('/print-stocker/{index?}', 'printStocker')->name('print-stocker');
        Route::post('/print-numbering/{index?}', 'printNumbering')->name('print-numbering');
    });

    //warehouse
    Route::controller(WarehouseController::class)->prefix("warehouse")->middleware('warehouse')->group(function () {
        Route::get('/', 'index')->name('warehouse');
    });

    //master lokasi
    Route::controller(MasterLokasiController::class)->prefix("master-lokasi")->middleware('master-lokasi')->group(function () {
        Route::get('/', 'index')->name('master-lokasi');
        Route::get('/create', 'create')->name('create-lokasi');
        Route::post('/store', 'store')->name('store-lokasi');
        Route::get('/update/{id?}', 'update')->name('update-lokasi');
        Route::get('/updatestatus', 'updatestatus')->name('updatestatus');
        Route::get('/simpanedit', 'simpanedit')->name('simpan-edit');
        Route::post('/print-lokasi/{id?}', 'printlokasi')->name('print-lokasi');
    });

    //Penerimaan
    Route::controller(InMaterialController::class)->prefix("in-material")->middleware('in-material')->group(function () {
        Route::get('/', 'index')->name('in-material');
        Route::get('/create', 'create')->name('create-inmaterial');
        Route::get('/lokasi-material/{id?}', 'lokmaterial')->name('lokasi-inmaterial');
        Route::get('/edit-material/{id?}', 'editmaterial')->name('edit-inmaterial');
        Route::post('/store', 'store')->name('store-inmaterial-fabric');
        Route::get('/updatedet', 'updatedet')->name('update-inmaterial-fabric');
        Route::get('/get-po', 'getPOList')->name('get-po-list');
        Route::get('/get-ws', 'getWSList')->name('get-ws-list');
        Route::get('/get-detail', 'getDetailList')->name('get-detail-list');
        Route::get('/get-detail-lok', 'getdetaillok')->name('get-detail-addlok');
        Route::get('/show-detail-lok', 'showdetaillok')->name('get-detail-showlok');
        Route::post('/save-lokasi', 'savelokasi')->name('save-lokasi');
        Route::get('/approve-material', 'approvematerial')->name('approve-material');
        Route::post('/print-barcode-inmaterial/{id?}', 'barcodeinmaterial')->name('print-barcode-inmaterial');
        Route::post('/print-pdf-inmaterial/{id?}', 'pdfinmaterial')->name('print-pdf-inmaterial');
    });

    //Pengeluaran
    Route::controller(OutMaterialController::class)->prefix("out-material")->middleware('out-material')->group(function () {
        Route::get('/', 'index')->name('out-material');
        Route::get('/create', 'create')->name('create-outmaterial');
        Route::get('/get-detail_req', 'getdetailreq')->name('get-detail_req');
        Route::get('/get-detail', 'getDetailList')->name('get-detail-item');
        Route::get('/show-detail-item', 'showdetailitem')->name('get-detail-showitem');
        Route::get('/get-list-barcode', 'getListbarcode')->name('get-list-barcode');
        Route::get('/get-data-barcode', 'showdetailbarcode')->name('get-data-barcode');
        Route::post('/save-out-manual', 'saveoutmanual')->name('save-out-manual');
        Route::post('/save-out-scan', 'saveoutscan')->name('save-out-scan');
        Route::post('/store', 'store')->name('store-outmaterial-fabric');
    });

 
    //mutasi-lokasi
    Route::controller(MutLokasiController::class)->prefix("mutasi-lokasi")->middleware('mutasi-lokasi')->group(function () {
        Route::get('/', 'index')->name('mutasi-lokasi');
        Route::get('/create', 'create')->name('create-mutlokasi');
        Route::get('/get-rak', 'getRakList')->name('get-rak-list');
        Route::get('/get-list-roll', 'getListroll')->name('get-list-roll');
        Route::get('/get-sum-roll', 'getSumroll')->name('get-sum-roll');
        Route::post('/store', 'store')->name('store-mutlokasi');
        Route::get('/approve-mutlok', 'approvemutlok')->name('approve-mutlok');
        Route::get('/edit-mutlok/{id?}', 'editmutlok')->name('edit-mutlok');
        Route::get('/update-mutlokasi', 'updatemutlok')->name('update-mutlokasi');
    });

    //qc pass
    Route::controller(QcPassController::class)->prefix("qc-pass")->middleware('qc-pass')->group(function () {
        Route::get('/', 'index')->name('qc-pass');
        Route::post('/store', 'store')->name('store-qcpass');
        Route::get('/get-data-item', 'getListItem')->name('get-data-item');
        Route::get('/get-data-item2', 'getListItem2')->name('get-data-item2');
        Route::get('/get-defect', 'getdefect')->name('get-defect');
        Route::get('/create-qcpass/{id?}', 'create')->name('create-qcpass');
        Route::post('/store-defect', 'storedefect')->name('store-defect');
        Route::post('/store-qcdet-temp', 'storeQcTemp')->name('store-qcdet-temp');
        Route::post('/store-qcdet-save', 'storeQcSave')->name('store-qcdet-save');
        Route::get('/get-detail-defect', 'getDetailList')->name('get-detail-defect');
        Route::get('/get-sum-data', 'getDataSum')->name('get-sum-data');
        Route::get('/get-avg-poin', 'getavgpoin')->name('get-avg-poin');
        Route::get('/get-poin', 'getpoin')->name('get-poin');
        Route::get('/finish-data', 'finishdata')->name('finish-data');
        Route::get('/finish-data-modal', 'finishdatamodal')->name('finish-data-modal');
        Route::get('/get_data_detailqc', 'getdatadetailqc')->name('get_data_detailqc');
        Route::get('/delete-qc-temp', 'deleteqctemp')->name('delete-qc-temp');
        Route::get('/show-qcpass/{id?}', 'showdata')->name('show-qcpass');
        Route::get('/export-qcpass/{id?}', 'exportdata')->name('export-qcpass');
        Route::get('/get-no-form', 'getnoform')->name('get-no-form');
        Route::get('/delete-qc-det', 'deleteqcdet')->name('delete-qc-det');
    });

});

Route::get('/dashboard-cutting', function () {
    return view('dashboard', ['page' => 'dashboard-cutting']);
})->middleware('auth')->name('dashboard-cutting');

Route::get('/dashboard-stocker', function () {
    return view('dashboard', ['page' => 'dashboard-stocker']);
})->middleware('auth')->name('dashboard-stocker');

//warehouse
Route::get('/dashboard-warehouse', function () {
    return view('dashboard', ['page' => 'dashboard-warehouse']);
})->middleware('auth')->name('dashboard-warehouse');

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

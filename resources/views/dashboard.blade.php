@if (!isset($page))
    @php
        $page = '';
    @endphp
@endif

@extends('layouts.index', ['page' => $page])

@section('custom-link')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-rowgroup/css/rowGroup.bootstrap4.min.css') }}">
    <!-- Apex Charts -->
    <link rel="stylesheet" href="{{ asset('plugins/apexcharts/apexcharts.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <style>
        .tooltip-inner {
            text-align: left !important;
        }
        .dataTables_wrapper .dataTables_processing {
            position: absolute;
            top: 35% !important;
        }
    </style>
@endsection

@section('content')
    <div style="{{ $page ? 'height: 100%;' : 'height: 75vh;' }}">
        @if ($page == 'dashboard-marker')
            @include('marker.dashboard', ["months" => $months, "years" => $years])
        @endif

        @if ($page == 'dashboard-cutting')
            @include('cutting.dashboard', ["months" => $months, "years" => $years])
        @endif

        @if ($page == 'dashboard-stocker')
            <div style="height: 75vh;"></div>
        @endif

        @if ($page == 'dashboard-dc')
            @include('dc.dashboard', ["months" => $months, "years" => $years])
        @endif

        @if ($page == 'dashboard-mut-karyawan')
            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        {{-- <div class="d-flex gap-3 justify-content-between mb-3">
                            <div class="d-flex gap-3">
                                <div>
                                    <label>Dari</label>
                                    <input class="form-control form-control-sm" type="date" id="date-from">
                                </div>
                                <div>
                                    <label>Sampai</label>
                                    <input class="form-control form-control-sm" type="date" id="date-to">
                                </div>
                            </div>
                        </div> --}}
                        <div id="chart"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- <div class="row">
        <div class="col-lg-12">
            <div class="card card-sb">
                <div class="card-header">
                    <h5 class="card-title">Card</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>150</h3>

                                    <p>New Orders</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <a href="#modal-default-1" data-bs-toggle="modal" class="small-box-footer">
                                    More info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <!-- small card -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>53<sup style="font-size: 20px">%</sup></h3>

                                    <p>Bounce Rate</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <a href="#modal-default-2" data-bs-toggle="modal" class="small-box-footer">
                                    More info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <!-- small card -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>44</h3>

                                    <p>User Registrations</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <a href="#modal-default-3" data-bs-toggle="modal" class="small-box-footer">
                                    More info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <!-- small card -->
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>65</h3>

                                    <p>Unique Visitors</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-pie"></i>
                                </div>
                                <a href="#modal-default-4" data-bs-toggle="modal" class="small-box-footer">
                                    More info <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card card-sb">
                <div class="card-header">
                    <h5 class="card-title m-0">Data</h5>
                </div>
                <div class="card-body">
                    <table id="datatable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Rendering engine</th>
                                <th>Browser</th>
                                <th>Platform(s)</th>
                                <th>Engine version</th>
                                <th>CSS grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 4.0
                            </td>
                                <td>Win 95+</td>
                                <td> 4</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 5.0
                            </td>
                                <td>Win 95+</td>
                                <td>5</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 5.5
                            </td>
                                <td>Win 95+</td>
                                <td>5.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 6
                            </td>
                                <td>Win 98+</td>
                                <td>6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet Explorer 7</td>
                                <td>Win XP SP2+</td>
                                <td>7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>AOL browser (AOL desktop)</td>
                                <td>Win XP</td>
                                <td>6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 1.0</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 1.5</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 2.0</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 3.0</td>
                                <td>Win 2k+ / OSX.3+</td>
                                <td>1.9</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Camino 1.0</td>
                                <td>OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Camino 1.5</td>
                                <td>OSX.3+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape 7.2</td>
                                <td>Win 95+ / Mac OS 8.6-9.2</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape Browser 8</td>
                                <td>Win 98SE+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape Navigator 9</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.0</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.1</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.2</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.2</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.3</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.4</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.4</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.5</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.6</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.7</td>
                                <td>Win 98+ / OSX.1+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.8</td>
                                <td>Win 98+ / OSX.1+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Seamonkey 1.1</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Epiphany 2.20</td>
                                <td>Gnome</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 1.2</td>
                                <td>OSX.3</td>
                                <td>125.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 1.3</td>
                                <td>OSX.3</td>
                                <td>312.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 2.0</td>
                                <td>OSX.4+</td>
                                <td>419.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 3.0</td>
                                <td>OSX.4+</td>
                                <td>522.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>OmniWeb 5.5</td>
                                <td>OSX.4+</td>
                                <td>420</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>iPod Touch / iPhone</td>
                                <td>iPod</td>
                                <td>420.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>S60</td>
                                <td>S60</td>
                                <td>413</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 7.0</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 7.5</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 8.0</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 8.5</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.0</td>
                                <td>Win 95+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.2</td>
                                <td>Win 88+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.5</td>
                                <td>Win 88+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera for Wii</td>
                                <td>Wii</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Nokia N800</td>
                                <td>N800</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Nintendo DS browser</td>
                                <td>Nintendo DS</td>
                                <td>8.5</td>
                                <td>C/A<sup>1</sup></td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.1</td>
                                <td>KDE 3.1</td>
                                <td>3.1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.3</td>
                                <td>KDE 3.3</td>
                                <td>3.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.5</td>
                                <td>KDE 3.5</td>
                                <td>3.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 4.5</td>
                                <td>Mac OS 8-9</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 5.1</td>
                                <td>Mac OS 7.6-9</td>
                                <td>1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 5.2</td>
                                <td>Mac OS 8-X</td>
                                <td>1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>NetFront 3.1</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>NetFront 3.4</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Dillo 0.8</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Links</td>
                                <td>Text only</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Lynx</td>
                                <td>Text only</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>IE Mobile</td>
                                <td>Windows Mobile 6</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>PSP browser</td>
                                <td>PSP</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Other browsers</td>
                                <td>All others</td>
                                <td>-</td>
                                <td>-</td>
                                <td>U</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Rendering engine</th>
                                <th>Browser</th>
                                <th>Platform(s)</th>
                                <th>Engine version</th>
                                <th>CSS grade</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-default-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Default Modal</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="datatable-1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Rendering engine</th>
                                <th>Browser</th>
                                <th>Platform(s)</th>
                                <th>Engine version</th>
                                <th>CSS grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 4.0
                            </td>
                                <td>Win 95+</td>
                                <td> 4</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 5.0
                            </td>
                                <td>Win 95+</td>
                                <td>5</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 5.5
                            </td>
                                <td>Win 95+</td>
                                <td>5.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 6
                            </td>
                                <td>Win 98+</td>
                                <td>6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet Explorer 7</td>
                                <td>Win XP SP2+</td>
                                <td>7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>AOL browser (AOL desktop)</td>
                                <td>Win XP</td>
                                <td>6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 1.0</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 1.5</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 2.0</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 3.0</td>
                                <td>Win 2k+ / OSX.3+</td>
                                <td>1.9</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Camino 1.0</td>
                                <td>OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Camino 1.5</td>
                                <td>OSX.3+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape 7.2</td>
                                <td>Win 95+ / Mac OS 8.6-9.2</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape Browser 8</td>
                                <td>Win 98SE+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape Navigator 9</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.0</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.1</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.2</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.2</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.3</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.4</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.4</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.5</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.6</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.7</td>
                                <td>Win 98+ / OSX.1+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.8</td>
                                <td>Win 98+ / OSX.1+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Seamonkey 1.1</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Epiphany 2.20</td>
                                <td>Gnome</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 1.2</td>
                                <td>OSX.3</td>
                                <td>125.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 1.3</td>
                                <td>OSX.3</td>
                                <td>312.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 2.0</td>
                                <td>OSX.4+</td>
                                <td>419.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 3.0</td>
                                <td>OSX.4+</td>
                                <td>522.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>OmniWeb 5.5</td>
                                <td>OSX.4+</td>
                                <td>420</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>iPod Touch / iPhone</td>
                                <td>iPod</td>
                                <td>420.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>S60</td>
                                <td>S60</td>
                                <td>413</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 7.0</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 7.5</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 8.0</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 8.5</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.0</td>
                                <td>Win 95+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.2</td>
                                <td>Win 88+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.5</td>
                                <td>Win 88+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera for Wii</td>
                                <td>Wii</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Nokia N800</td>
                                <td>N800</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Nintendo DS browser</td>
                                <td>Nintendo DS</td>
                                <td>8.5</td>
                                <td>C/A<sup>1</sup></td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.1</td>
                                <td>KDE 3.1</td>
                                <td>3.1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.3</td>
                                <td>KDE 3.3</td>
                                <td>3.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.5</td>
                                <td>KDE 3.5</td>
                                <td>3.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 4.5</td>
                                <td>Mac OS 8-9</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 5.1</td>
                                <td>Mac OS 7.6-9</td>
                                <td>1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 5.2</td>
                                <td>Mac OS 8-X</td>
                                <td>1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>NetFront 3.1</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>NetFront 3.4</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Dillo 0.8</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Links</td>
                                <td>Text only</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Lynx</td>
                                <td>Text only</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>IE Mobile</td>
                                <td>Windows Mobile 6</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>PSP browser</td>
                                <td>PSP</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Other browsers</td>
                                <td>All others</td>
                                <td>-</td>
                                <td>-</td>
                                <td>U</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Rendering engine</th>
                                <th>Browser</th>
                                <th>Platform(s)</th>
                                <th>Engine version</th>
                                <th>CSS grade</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-default-2">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Default Modal</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="datatable-2" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Rendering engine</th>
                                <th>Browser</th>
                                <th>Platform(s)</th>
                                <th>Engine version</th>
                                <th>CSS grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 4.0
                            </td>
                                <td>Win 95+</td>
                                <td> 4</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 5.0
                            </td>
                                <td>Win 95+</td>
                                <td>5</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 5.5
                            </td>
                                <td>Win 95+</td>
                                <td>5.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 6
                            </td>
                                <td>Win 98+</td>
                                <td>6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet Explorer 7</td>
                                <td>Win XP SP2+</td>
                                <td>7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>AOL browser (AOL desktop)</td>
                                <td>Win XP</td>
                                <td>6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 1.0</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 1.5</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 2.0</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 3.0</td>
                                <td>Win 2k+ / OSX.3+</td>
                                <td>1.9</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Camino 1.0</td>
                                <td>OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Camino 1.5</td>
                                <td>OSX.3+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape 7.2</td>
                                <td>Win 95+ / Mac OS 8.6-9.2</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape Browser 8</td>
                                <td>Win 98SE+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape Navigator 9</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.0</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.1</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.2</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.2</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.3</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.4</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.4</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.5</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.6</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.7</td>
                                <td>Win 98+ / OSX.1+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.8</td>
                                <td>Win 98+ / OSX.1+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Seamonkey 1.1</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Epiphany 2.20</td>
                                <td>Gnome</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 1.2</td>
                                <td>OSX.3</td>
                                <td>125.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 1.3</td>
                                <td>OSX.3</td>
                                <td>312.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 2.0</td>
                                <td>OSX.4+</td>
                                <td>419.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 3.0</td>
                                <td>OSX.4+</td>
                                <td>522.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>OmniWeb 5.5</td>
                                <td>OSX.4+</td>
                                <td>420</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>iPod Touch / iPhone</td>
                                <td>iPod</td>
                                <td>420.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>S60</td>
                                <td>S60</td>
                                <td>413</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 7.0</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 7.5</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 8.0</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 8.5</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.0</td>
                                <td>Win 95+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.2</td>
                                <td>Win 88+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.5</td>
                                <td>Win 88+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera for Wii</td>
                                <td>Wii</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Nokia N800</td>
                                <td>N800</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Nintendo DS browser</td>
                                <td>Nintendo DS</td>
                                <td>8.5</td>
                                <td>C/A<sup>1</sup></td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.1</td>
                                <td>KDE 3.1</td>
                                <td>3.1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.3</td>
                                <td>KDE 3.3</td>
                                <td>3.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.5</td>
                                <td>KDE 3.5</td>
                                <td>3.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 4.5</td>
                                <td>Mac OS 8-9</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 5.1</td>
                                <td>Mac OS 7.6-9</td>
                                <td>1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 5.2</td>
                                <td>Mac OS 8-X</td>
                                <td>1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>NetFront 3.1</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>NetFront 3.4</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Dillo 0.8</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Links</td>
                                <td>Text only</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Lynx</td>
                                <td>Text only</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>IE Mobile</td>
                                <td>Windows Mobile 6</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>PSP browser</td>
                                <td>PSP</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Other browsers</td>
                                <td>All others</td>
                                <td>-</td>
                                <td>-</td>
                                <td>U</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Rendering engine</th>
                                <th>Browser</th>
                                <th>Platform(s)</th>
                                <th>Engine version</th>
                                <th>CSS grade</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-default-3">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Default Modal</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="datatable-3" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Rendering engine</th>
                                <th>Browser</th>
                                <th>Platform(s)</th>
                                <th>Engine version</th>
                                <th>CSS grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 4.0
                            </td>
                                <td>Win 95+</td>
                                <td> 4</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 5.0
                            </td>
                                <td>Win 95+</td>
                                <td>5</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 5.5
                            </td>
                                <td>Win 95+</td>
                                <td>5.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 6
                            </td>
                                <td>Win 98+</td>
                                <td>6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet Explorer 7</td>
                                <td>Win XP SP2+</td>
                                <td>7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>AOL browser (AOL desktop)</td>
                                <td>Win XP</td>
                                <td>6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 1.0</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 1.5</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 2.0</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 3.0</td>
                                <td>Win 2k+ / OSX.3+</td>
                                <td>1.9</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Camino 1.0</td>
                                <td>OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Camino 1.5</td>
                                <td>OSX.3+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape 7.2</td>
                                <td>Win 95+ / Mac OS 8.6-9.2</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape Browser 8</td>
                                <td>Win 98SE+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape Navigator 9</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.0</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.1</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.2</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.2</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.3</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.4</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.4</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.5</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.6</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.7</td>
                                <td>Win 98+ / OSX.1+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.8</td>
                                <td>Win 98+ / OSX.1+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Seamonkey 1.1</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Epiphany 2.20</td>
                                <td>Gnome</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 1.2</td>
                                <td>OSX.3</td>
                                <td>125.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 1.3</td>
                                <td>OSX.3</td>
                                <td>312.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 2.0</td>
                                <td>OSX.4+</td>
                                <td>419.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 3.0</td>
                                <td>OSX.4+</td>
                                <td>522.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>OmniWeb 5.5</td>
                                <td>OSX.4+</td>
                                <td>420</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>iPod Touch / iPhone</td>
                                <td>iPod</td>
                                <td>420.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>S60</td>
                                <td>S60</td>
                                <td>413</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 7.0</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 7.5</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 8.0</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 8.5</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.0</td>
                                <td>Win 95+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.2</td>
                                <td>Win 88+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.5</td>
                                <td>Win 88+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera for Wii</td>
                                <td>Wii</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Nokia N800</td>
                                <td>N800</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Nintendo DS browser</td>
                                <td>Nintendo DS</td>
                                <td>8.5</td>
                                <td>C/A<sup>1</sup></td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.1</td>
                                <td>KDE 3.1</td>
                                <td>3.1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.3</td>
                                <td>KDE 3.3</td>
                                <td>3.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.5</td>
                                <td>KDE 3.5</td>
                                <td>3.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 4.5</td>
                                <td>Mac OS 8-9</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 5.1</td>
                                <td>Mac OS 7.6-9</td>
                                <td>1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 5.2</td>
                                <td>Mac OS 8-X</td>
                                <td>1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>NetFront 3.1</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>NetFront 3.4</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Dillo 0.8</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Links</td>
                                <td>Text only</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Lynx</td>
                                <td>Text only</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>IE Mobile</td>
                                <td>Windows Mobile 6</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>PSP browser</td>
                                <td>PSP</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Other browsers</td>
                                <td>All others</td>
                                <td>-</td>
                                <td>-</td>
                                <td>U</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Rendering engine</th>
                                <th>Browser</th>
                                <th>Platform(s)</th>
                                <th>Engine version</th>
                                <th>CSS grade</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-default-4">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Default Modal</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="datatable-4" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Rendering engine</th>
                                <th>Browser</th>
                                <th>Platform(s)</th>
                                <th>Engine version</th>
                                <th>CSS grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 4.0
                            </td>
                                <td>Win 95+</td>
                                <td> 4</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 5.0
                            </td>
                                <td>Win 95+</td>
                                <td>5</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 5.5
                            </td>
                                <td>Win 95+</td>
                                <td>5.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet
                                Explorer 6
                            </td>
                                <td>Win 98+</td>
                                <td>6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>Internet Explorer 7</td>
                                <td>Win XP SP2+</td>
                                <td>7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Trident</td>
                                <td>AOL browser (AOL desktop)</td>
                                <td>Win XP</td>
                                <td>6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 1.0</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 1.5</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 2.0</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Firefox 3.0</td>
                                <td>Win 2k+ / OSX.3+</td>
                                <td>1.9</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Camino 1.0</td>
                                <td>OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Camino 1.5</td>
                                <td>OSX.3+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape 7.2</td>
                                <td>Win 95+ / Mac OS 8.6-9.2</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape Browser 8</td>
                                <td>Win 98SE+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Netscape Navigator 9</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.0</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.1</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.2</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.2</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.3</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.4</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.4</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.5</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.6</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>1.6</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.7</td>
                                <td>Win 98+ / OSX.1+</td>
                                <td>1.7</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Mozilla 1.8</td>
                                <td>Win 98+ / OSX.1+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Seamonkey 1.1</td>
                                <td>Win 98+ / OSX.2+</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Gecko</td>
                                <td>Epiphany 2.20</td>
                                <td>Gnome</td>
                                <td>1.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 1.2</td>
                                <td>OSX.3</td>
                                <td>125.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 1.3</td>
                                <td>OSX.3</td>
                                <td>312.8</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 2.0</td>
                                <td>OSX.4+</td>
                                <td>419.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>Safari 3.0</td>
                                <td>OSX.4+</td>
                                <td>522.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>OmniWeb 5.5</td>
                                <td>OSX.4+</td>
                                <td>420</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>iPod Touch / iPhone</td>
                                <td>iPod</td>
                                <td>420.1</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Webkit</td>
                                <td>S60</td>
                                <td>S60</td>
                                <td>413</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 7.0</td>
                                <td>Win 95+ / OSX.1+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 7.5</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 8.0</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 8.5</td>
                                <td>Win 95+ / OSX.2+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.0</td>
                                <td>Win 95+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.2</td>
                                <td>Win 88+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera 9.5</td>
                                <td>Win 88+ / OSX.3+</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Opera for Wii</td>
                                <td>Wii</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Nokia N800</td>
                                <td>N800</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Presto</td>
                                <td>Nintendo DS browser</td>
                                <td>Nintendo DS</td>
                                <td>8.5</td>
                                <td>C/A<sup>1</sup></td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.1</td>
                                <td>KDE 3.1</td>
                                <td>3.1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.3</td>
                                <td>KDE 3.3</td>
                                <td>3.3</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>KHTML</td>
                                <td>Konqureror 3.5</td>
                                <td>KDE 3.5</td>
                                <td>3.5</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 4.5</td>
                                <td>Mac OS 8-9</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 5.1</td>
                                <td>Mac OS 7.6-9</td>
                                <td>1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Tasman</td>
                                <td>Internet Explorer 5.2</td>
                                <td>Mac OS 8-X</td>
                                <td>1</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>NetFront 3.1</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>NetFront 3.4</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>A</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Dillo 0.8</td>
                                <td>Embedded devices</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Links</td>
                                <td>Text only</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>Lynx</td>
                                <td>Text only</td>
                                <td>-</td>
                                <td>X</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>IE Mobile</td>
                                <td>Windows Mobile 6</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Misc</td>
                                <td>PSP browser</td>
                                <td>PSP</td>
                                <td>-</td>
                                <td>C</td>
                            </tr>
                            <tr>
                                <td>Other browsers</td>
                                <td>All others</td>
                                <td>-</td>
                                <td>-</td>
                                <td>U</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Rendering engine</th>
                                <th>Browser</th>
                                <th>Platform(s)</th>
                                <th>Engine version</th>
                                <th>CSS grade</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@section('custom-script')
    <!-- DataTables & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-rowgroup/js/dataTables.rowGroup.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-rowgroup/js/rowGroup.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-rowsgroup/dataTables.rowsGroup.js') }}"></script>
    <!-- Apex Charts -->
    <script src="{{ asset('plugins/apexcharts/apexcharts.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

    <!-- Page specific script -->
    <script>
        $(document).ready(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4',
            });

            $("#datatable").DataTable({
                "responsive": true,
                "autoWidth": false,
            });

            $("#datatable-1").DataTable({
                "responsive": true,
                "autoWidth": false,
            })

            $("#datatable-2").DataTable({
                "responsive": true,
                "autoWidth": false,
            })

            $("#datatable-3").DataTable({
                "responsive": true,
                "autoWidth": false,
            })

            $("#datatable-4").DataTable({
                "responsive": true,
                "autoWidth": false,
            })
        });
    </script>

    @if ($page == 'dashboard-marker')
        <script>
            $(document).ready(async function() {
                let today = new Date();
                let todayDate = ("0" + today.getDate()).slice(-2);
                let todayMonth = ("0" + (today.getMonth() + 1)).slice(-2);
                let todayYear = today.getFullYear();
                let todayFullDate = todayYear + '-' + todayMonth + '-' + todayDate;

                // Marker Datatable
                $('#marker-month-filter').val((today.getMonth() + 1)).trigger("change");
                $('#marker-year-filter').val(todayYear).trigger("change");

                var datatableMarker = $("#datatable-marker").DataTable({
                    serveSide: true,
                    processing: true,
                    ordering: false,
                    pageLength: 50,
                    ajax: {
                        url: '{{ route('dashboard-marker') }}',
                        dataType: 'json',
                        data: function (d) {
                            d.month = $('#marker-month-filter').val();
                            d.year = $('#marker-year-filter').val();
                        }
                    },
                    columns: [
                        {
                            data: 'tgl_cutting',
                        },
                        {
                            data: 'buyer',
                        },
                        {
                            data: 'act_costing_ws',
                        },
                        {
                            data: 'style',
                        },
                        {
                            data: 'color',
                        },
                        {
                            data: 'panel',
                        },
                        {
                            data: 'kode',
                        },
                        {
                            data: 'urutan_marker',
                        },
                        {
                            data: 'gelar_qty',
                        },
                        {
                            data: 'marker_details',
                        },
                        {
                            data: 'nama_part',
                        }
                    ],
                    columnDefs: [
                        // {
                        //     targets: [0 ,1 ,2 ,3 ,4 ,5 , 6, 7, 8, 9],
                        //     className: "text-nowrap align-middle"
                        // },
                        {
                            targets: "_all",
                            className: "text-nowrap colorize"
                        }
                    ],
                    // rowGroup: {
                    //     dataSrc: ['tgl_cutting', 'buyer', 'act_costing_ws', 'style', 'color', 'panel', 'kode', 'urutan_marker', 'gelar_qty', 'marker_details',]
                    // },
                    rowsGroup: [0 ,1 ,2 ,3 ,4 ,5 , 6, 7, 8, 9]
                    // rowCallback: function( row, data, index ) {
                    //     if (data['line'] != '-') {
                    //         $('td.colorize', row).css('color', '#2e8a57');
                    //         $('td.colorize', row).css('font-weight', '600');
                    //     } else if (!data['dc_in_id'] && data['troli'] == '-') {
                    //         $('td.colorize', row).css('color', '#da4f4a');
                    //         $('td.colorize', row).css('font-weight', '600');
                    //     }
                    // }
                });

                $('#datatable-marker thead tr').clone(true).appendTo('#datatable-marker thead');
                $('#datatable-marker thead tr:eq(1) th').each(function(i) {
                    var title = $(this).text();
                    $(this).html('<input type="text" class="form-control form-control-sm"/>');

                    $('input', this).on('keyup change', function() {
                        if (datatableMarker.column(i).search() !== this.value) {
                            datatableMarker
                                .column(i)
                                .search(this.value)
                                .draw();
                        }
                    });
                });

                $('#marker-month-filter').on('change', () => {
                    $('#datatable-marker').DataTable().ajax.reload();
                });
                $('#marker-year-filter').on('change', () => {
                    $('#datatable-marker').DataTable().ajax.reload();
                });

                // Marker Qty
                $('#markerqty-month-filter').val((today.getMonth() + 1)).trigger("change");
                $('#markerqty-year-filter').val(todayYear).trigger("change");
                await getMarkerQty();

                $('#markerqty-month-filter').on('change', () => {
                    getMarkerQty();
                });
                $('#markerqty-year-filter').on('change', () => {
                    getMarkerQty();
                });
            });

            function getMarkerQty() {
                document.getElementById("marker-qty-data").classList.add("d-none");
                document.getElementById("loading-marker-qty").classList.remove("d-none");

                return $.ajax({
                    url: '{{ route('marker-qty') }}',
                    type: 'get',
                    data: {
                        month : $('#markerqty-month-filter').val(),
                        year : $('#markerqty-year-filter').val()
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res) {
                            let wsCount = res['wsQty'] ? res['wsQty'] : 0;
                            let partCount = res['partQty'] ? res['partQty'] : 0;
                            let markerCount = res['markerQty'] ? res['markerQty'] : 0;
                            let markerSum = res['markerSum'] ? res['markerSum'] : 0;

                            document.getElementById("ws-qty").innerText = Number(wsCount).toLocaleString('ID-id');
                            document.getElementById("part-qty").innerText = Number(partCount).toLocaleString('ID-id');
                            document.getElementById("marker-qty").innerText = Number(markerCount).toLocaleString('ID-id');
                            document.getElementById("marker-sum").innerText = Number(markerSum).toLocaleString('ID-id');
                        }

                        document.getElementById("marker-qty-data").classList.remove("d-none");
                        document.getElementById("loading-marker-qty").classList.add("d-none");
                    },
                    error: function(jqXHR) {
                        console.log(jqXHR);

                        document.getElementById("marker-qty-data").classList.remove("d-none");
                        document.getElementById("loading-marker-qty").classList.add("d-none");
                    }
                });
            }
        </script>
    @endif

    @if ($page == 'dashboard-cutting')
        <script>
            $(document).ready(async function() {
                let today = new Date();
                let todayDate = ("0" + today.getDate()).slice(-2);
                let todayMonth = ("0" + (today.getMonth() + 1)).slice(-2);
                let todayYear = today.getFullYear();
                let todayFullDate = todayYear + '-' + todayMonth + '-' + todayDate;

                // Cutting Datatable
                $('#cutting-month-filter').val((today.getMonth() + 1)).trigger("change");
                $('#cutting-year-filter').val(todayYear).trigger("change");

                var datatableCutting = $("#datatable-cutting").DataTable({
                    serveSide: true,
                    processing: true,
                    ordering: false,
                    pageLength: 50,
                    ajax: {
                        url: '{{ route('dashboard-cutting') }}',
                        dataType: 'json',
                        data: function (d) {
                            d.month = $('#cutting-month-filter').val();
                            d.year = $('#cutting-year-filter').val();
                        }
                    },
                    columns: [
                        {
                            data: 'tgl_form_cut',
                        },
                        {
                            data: 'buyer',
                        },
                        {
                            data: 'act_costing_ws',
                        },
                        {
                            data: 'style',
                        },
                        {
                            data: 'color',
                        },
                        {
                            data: 'kode',
                        },
                        {
                            data: 'urutan_marker',
                        },
                        {
                            data: 'panel',
                        },
                        {
                            data: 'no_form',
                        },
                        {
                            data: 'no_cut',
                        },
                        {
                            data: 'total_lembar',
                        },
                        {
                            data: 'id_roll',
                        },
                        {
                            data: 'id_item',
                        },
                        {
                            data: 'detail_item',
                        },
                        {
                            data: 'group_roll',
                        },
                        {
                            data: 'lot',
                        },
                        {
                            data: 'roll',
                        },
                        {
                            data: 'qty',
                        },
                        {
                            data: 'total_pemakaian_roll',
                        },
                        {
                            data: 'piping',
                        },
                        {
                            data: 'short_roll',
                        },
                        {
                            data: 'remark',
                        },
                        {
                            data: 'unit',
                        }
                    ],
                    columnDefs: [
                        {
                            targets: [0 ,1 ,2 ,3 ,4 ,5 , 6, 7, 8, 9, 10],
                            className: "text-nowrap align-middle"
                        },
                        {
                            targets: "_all",
                            className: "text-nowrap colorize"
                        }
                    ],
                    rowsGroup: [
                        0,
                        1,
                        2,
                        3,
                        4,
                        5,
                        6,
                        7,
                        8,
                        9,
                        10
                    ],
                    // rowCallback: function( row, data, index ) {
                    //     if (data['line'] != '-') {
                    //         $('td.colorize', row).css('color', '#2e8a57');
                    //         $('td.colorize', row).css('font-weight', '600');
                    //     } else if (!data['dc_in_id'] && data['troli'] == '-') {
                    //         $('td.colorize', row).css('color', '#da4f4a');
                    //         $('td.colorize', row).css('font-weight', '600');
                    //     }
                    // }
                });

                $('#datatable-cutting thead tr').clone(true).appendTo('#datatable-cutting thead');
                $('#datatable-cutting thead tr:eq(1) th').each(function(i) {
                    var title = $(this).text();
                    $(this).html('<input type="text" class="form-control form-control-sm"/>');

                    $('input', this).on('keyup change', function() {
                        if (datatableCutting.column(i).search() !== this.value) {
                            datatableCutting
                                .column(i)
                                .search(this.value)
                                .draw();
                        }
                    });
                });

                $('#cutting-month-filter').on('change', () => {
                    $('#datatable-cutting').DataTable().ajax.reload();
                });
                $('#cutting-year-filter').on('change', () => {
                    $('#datatable-cutting').DataTable().ajax.reload();
                });

                // Cutting Qty
                $('#cuttingqty-month-filter').val((today.getMonth() + 1)).trigger("change");
                $('#cuttingqty-year-filter').val(todayYear).trigger("change");
                await getCuttingQty();

                $('#cuttingqty-month-filter').on('change', () => {
                    getCuttingQty();
                });
                $('#cuttingqty-year-filter').on('change', () => {
                    getCuttingQty();
                });
            });

            function getCuttingQty() {
                document.getElementById("cutting-qty-data").classList.add("d-none");
                document.getElementById("loading-cutting-qty").classList.remove("d-none");

                return $.ajax({
                    url: '{{ route('cutting-qty') }}',
                    type: 'get',
                    data: {
                        month : $('#cuttingqty-month-filter').val(),
                        year : $('#cuttingqty-year-filter').val()
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res && res[0]) {
                            let pendingQty = res[0].pending ? res[0].pending : 0;
                            let pendingTotal = res[0].pending_total ? res[0].pending_total : 0;
                            let planQty = res[0].plan ? res[0].plan : 0;
                            let planTotal = res[0].plan_total ? res[0].plan_total : 0;
                            let progressQty = res[0].progress ? res[0].progress : 0;
                            let progressTotal = res[0].progress_total ? res[0].progress_total : 0;
                            let finishedQty = res[0].finished ? res[0].finished : 0;
                            let finishedTotal = res[0].finished_total ? res[0].finished_total : 0;

                            console.log(pendingQty, pendingTotal, planQty, planTotal, progressQty, progressTotal, finishedQty, finishedTotal)

                            document.getElementById("pending-qty").innerText = pendingQty.toLocaleString('ID-id');
                            document.getElementById("pending-total").innerText = pendingTotal.toLocaleString('ID-id');
                            document.getElementById("plan-qty").innerText = planQty.toLocaleString('ID-id');
                            document.getElementById("plan-total").innerText = planTotal.toLocaleString('ID-id');
                            document.getElementById("progress-qty").innerText = progressQty.toLocaleString('ID-id');
                            document.getElementById("progress-total").innerText = progressTotal.toLocaleString('ID-id');
                            document.getElementById("finished-qty").innerText = finishedQty.toLocaleString('ID-id');
                            document.getElementById("finished-total").innerText = finishedTotal.toLocaleString('ID-id');
                        }

                        document.getElementById("cutting-qty-data").classList.remove("d-none");
                        document.getElementById("loading-cutting-qty").classList.add("d-none");
                    },
                    error: function(jqXHR) {
                        console.log(jqXHR);

                        document.getElementById("cutting-qty-data").classList.remove("d-none");
                        document.getElementById("loading-cutting-qty").classList.add("d-none");
                    }
                });
            }
        </script>
    @endif

    @if ($page == 'dashboard-dc')
        <script>
            $(document).ready(async function() {
                let today = new Date();
                let todayDate = ("0" + today.getDate()).slice(-2);
                let todayMonth = ("0" + (today.getMonth() + 1)).slice(-2);
                let todayYear = today.getFullYear();
                let todayFullDate = todayYear + '-' + todayMonth + '-' + todayDate;

                // DC Datatable
                $('#dc-month-filter').val((today.getMonth() + 1)).trigger("change");
                $('#dc-year-filter').val(todayYear).trigger("change");

                var datatableDc = $("#datatable-dc").DataTable({
                    serveSide: true,
                    processing: true,
                    ordering: false,
                    pageLength: 50,
                    ajax: {
                        url: '{{ route('dashboard-dc') }}',
                        dataType: 'json',
                        data: function (d) {
                            d.month = $('#dc-month-filter').val();
                            d.year = $('#dc-year-filter').val();
                        }
                    },
                    columns: [
                        {
                            data: 'act_costing_ws',
                        },
                        {
                            data: 'color',
                        },
                        {
                            data: 'no_cut',
                        },
                        {
                            data: 'nama_part',
                        },
                        {
                            data: 'size',
                        },
                        {
                            data: 'shade',
                        },
                        {
                            data: 'id_qr_stocker',
                        },
                        {
                            data: 'stocker_range',
                        },
                        {
                            data: 'secondary',
                        },
                        {
                            data: 'rak',
                        },
                        {
                            data: 'troli',
                        },
                        {
                            data: 'line',
                        },
                        {
                            data: 'dc_in_qty',
                        },
                        {
                            data: 'latest_update',
                        },
                    ],
                    columnDefs: [
                        {
                            targets: [0, 1, 2, 3, 4, 5],
                            className: "text-nowrap text-center align-middle"
                        },
                        {
                            targets: "_all",
                            className: "text-nowrap colorize"
                        }
                    ],
                    rowsGroup: [
                        0,
                        1,
                        2,
                        3,
                        4,
                        5
                    ],
                    rowCallback: function( row, data, index ) {
                        if (data['line'] != '-') {
                            $('td.colorize', row).css('color', '#2e8a57');
                            $('td.colorize', row).css('font-weight', '600');
                        } else if (!data['dc_in_id'] && data['troli'] == '-') {
                            $('td.colorize', row).css('color', '#da4f4a');
                            $('td.colorize', row).css('font-weight', '600');
                        }
                    }
                });

                $('#datatable-dc thead tr').clone(true).appendTo('#datatable-dc thead');
                $('#datatable-dc thead tr:eq(1) th').each(function(i) {
                    var title = $(this).text();
                    $(this).html('<input type="text" class="form-control form-control-sm"/>');

                    $('input', this).on('keyup change', function() {
                        if (datatableDc.column(i).search() !== this.value) {
                            datatableDc
                                .column(i)
                                .search(this.value)
                                .draw();
                        }
                    });
                });

                $('#dc-month-filter').on('change', () => {
                    $('#datatable-dc').DataTable().ajax.reload();
                });
                $('#dc-year-filter').on('change', () => {
                    $('#datatable-dc').DataTable().ajax.reload();
                });

                // DC Qty
                $('#dcqty-month-filter').val((today.getMonth() + 1)).trigger("change");
                $('#dcqty-year-filter').val(todayYear).trigger("change");
                await getDcQty();

                $('#dcqty-month-filter').on('change', () => {
                    getDcQty();
                });
                $('#dcqty-year-filter').on('change', () => {
                    getDcQty();
                });
            });

            function getDcQty() {
                document.getElementById("dc-qty-data").classList.add("d-none");
                document.getElementById("loading-dc-qty").classList.remove("d-none");

                return $.ajax({
                    url: '{{ route('dc-qty') }}',
                    type: 'get',
                    data: {
                        month : $('#dcqty-month-filter').val(),
                        year : $('#dcqty-year-filter').val()
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res) {
                            let totalStocker = 0;
                            let totalSecondary = 0;
                            let totalRak = 0;
                            let totalTroli = 0;
                            let totalLine = 0;

                            res.forEach(item => {
                                if (item.secondary == "-" && item.rak == "-" && item.troli == "-" && item.line == "-") {
                                    totalStocker += item.dc_in_qty;
                                } else if (item.secondary != "-" && item.rak == "-" && item.troli == "-" && item.line == "-") {
                                    totalSecondary += item.dc_in_qty;
                                } else if (item.rak != "-" && item.troli == "-" && item.line == "-") {
                                    totalRak += item.dc_in_qty;
                                } else if (item.troli != "-" && item.line == "-") {
                                    totalTroli += item.dc_in_qty;
                                } else if (item.line != "-") {
                                    totalLine += item.dc_in_qty;
                                }
                            });

                            document.getElementById("stocker-qty").innerText = totalStocker.toLocaleString('ID-id');
                            document.getElementById("secondary-qty").innerText = totalSecondary.toLocaleString('ID-id');
                            document.getElementById("non-secondary-qty").innerText = (totalRak + totalTroli).toLocaleString('ID-id');
                            document.getElementById("line-qty").innerText = totalLine.toLocaleString('ID-id');
                        }

                        document.getElementById("dc-qty-data").classList.remove("d-none");
                        document.getElementById("loading-dc-qty").classList.add("d-none");
                    },
                    error: function(jqXHR) {
                        console.log(jqXHR);

                        document.getElementById("dc-qty-data").classList.remove("d-none");
                        document.getElementById("loading-dc-qty").classList.add("d-none");
                    }
                });
            }
        </script>
    @endif

    @if ($page == 'dashboard-mut-karyawan')
        <script>
            function autoBreak(label) {
                const maxLength = 5;
                const lines = [];

                for (let word of label.split(" ")) {
                    if (lines.length == 0) {
                        lines.push(word);
                    } else {
                        const i = lines.length - 1
                        const line = lines[i]

                        if (line.length + 1 + word.length <= maxLength) {
                            lines[i] = `${line} ${word}`
                        } else {
                            lines.push(word)
                        }
                    }
                }

                return lines;
            }

            document.addEventListener('DOMContentLoaded', () => {
                // bar chart options
                var options = {
                    chart: {
                        height: 550,
                        type: 'bar',
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            dataLabels: {
                                position: 'top',
                            },
                            colors: {
                                ranges: [{
                                    from: 0,
                                    to: 100,
                                    color: '#1640D6'
                                }],
                                backgroundBarColors: [],
                                backgroundBarOpacity: 1,
                                backgroundBarRadius: 0,
                            },
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: {
                            colors: ['#333']
                        },
                        formatter: function(val, opts) {
                            return val.toLocaleString()
                        },
                        offsetY: -30
                    },
                    series: [],
                    xaxis: {
                        labels: {
                            show: true,
                            rotate: 0,
                            rotateAlways: false,
                            hideOverlappingLabels: false,
                            showDuplicates: false,
                            trim: false,
                            minHeight: undefined,
                            style: {
                                fontSize: '12px',
                                fontFamily: 'Helvetica, Arial, sans-serif',
                                fontWeight: 600,
                                cssClass: 'apexcharts-xaxis-label',
                            },
                        }
                    },
                    title: {
                        text: 'Data Line ',
                        align: 'center',
                        style: {
                            fontSize: '18px',
                            fontWeight: 'bold',
                            fontFamily: undefined,
                            color: '#263238'
                        },
                    },
                    noData: {
                        text: 'Loading...'
                    }
                }
                var chart = new ApexCharts(
                    document.querySelector("#chart"),
                    options
                );
                chart.render();

                // fetch order defect data function
                function getLineData() {
                    $.ajax({
                        url: '{{ route('line-chart-data') }}',
                        type: 'get',
                        dataType: 'json',
                        success: function(res) {
                            let totalEmployee = 0;
                            let dataArr = [];
                            res.forEach(element => {
                                totalEmployee += element.tot_orang;
                                dataArr.push({
                                    'x': autoBreak(element.line),
                                    'y': element.tot_orang
                                });
                            });

                            chart.updateSeries([{
                                name: "Karyawan Line",
                                data: dataArr
                            }], true);

                            chart.updateOptions({
                                title: {
                                    text: "Data Line",
                                    align: 'center',
                                    style: {
                                        fontSize: '18px',
                                        fontWeight: 'bold',
                                        fontFamily: undefined,
                                        color: '#263238'
                                    },
                                },
                                subtitle: {
                                    // text: [dari+' / '+sampai, 'Total Orang : '+totalEmployee.toLocaleString()],
                                    text: ['Total Orang : ' + totalEmployee.toLocaleString()],
                                    align: 'center',
                                    style: {
                                        fontSize: '13px',
                                        fontFamily: undefined,
                                        color: '#263238'
                                    },
                                }
                            });
                        },
                        error: function(jqXHR) {
                            let res = jqXHR.responseJSON;
                            console.error(res.message);
                            iziToast.error({
                                title: 'Error',
                                message: res.message,
                                position: 'topCenter'
                            });
                        }
                    });
                }

                // initial fetch
                // let today = new Date();
                // let todayDate = ("0" + today.getDate()).slice(-2);
                // let todayMonth = ("0" + (today.getMonth() + 1)).slice(-2);
                // let todayYear = today.getFullYear();
                // let todayFull = todayYear+'-'+todayMonth+'-'+todayDate;
                // let twoWeeksBefore = new Date(new Date().setDate(new Date().getDate() - 14));
                // let twoWeeksBeforeDate = ("0" + twoWeeksBefore.getDate()).slice(-2);
                // let twoWeeksBeforeMonth = ("0" + (twoWeeksBefore.getMonth() + 1)).slice(-2);
                // let twoWeeksBeforeYear = twoWeeksBefore.getFullYear();
                // let twoWeeksBeforeFull = twoWeeksBeforeYear+'-'+twoWeeksBeforeMonth+'-'+twoWeeksBeforeDate;
                // $('#date-to').val(todayFull);
                // $('#date-from').val(twoWeeksBeforeFull);

                getLineData()

                // fetch on select supplier
                // $('#supplier').on('select2:select', function (e) {
                //     getOrderDefectData(e.params.data.element.value, e.params.data.element.innerText, $('#date-from').val(), $('#date-to').val());
                // });

                // fetch on select date
                // $('#date-from').change(function (e) {
                //     updateBuyerList();
                //     getOrderDefectData($('#supplier').val(), $('#supplier option:selected').text(), $('#date-from').val(), $('#date-to').val());
                // });

                // $('#date-to').change(function (e) {
                //     updateBuyerList();
                //     getOrderDefectData($('#supplier').val(), $('#supplier option:selected').text(), $('#date-from').val(), $('#date-to').val());
                // });

                // fetch every 30 second
                setInterval(function() {
                    getLineData();
                }, 30000)
            });
        </script>
    @endif
@endsection

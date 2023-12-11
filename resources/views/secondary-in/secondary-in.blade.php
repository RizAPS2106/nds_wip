@extends('layouts.index')

@section('custom-link')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="modal fade" id="exampleModalEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalEditLabel"
        aria-hidden="true">
        <form action="{{ route('update_tmp_dc_in') }}" method="post" onsubmit="submitForm(this, event)">
            @method('PUT')
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-sb text-light">
                        <h1 class="modal-title fs-5">Scan Qr Secondary In</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row justify-content-center align-items-end">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label label-input">Scan QR Stocker</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm border-input"
                                            name="txtqrstocker" id="txtqrstocker" autocomplete="off" enterkeyhint="go"
                                            onkeyup="if (event.keyCode == 13)
                                        document.getElementById('scanqr').click()"
                                            autofocus>
                                        {{-- <input type="button" class="btn btn-sm btn-primary" value="Scan Line" /> --}}
                                        {{-- style="display: none;" --}}
                                        <button class="btn btn-sm btn-primary" type="button" id="scanqr"
                                            onclick="scan_qr()">Scan</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div></div>
                            </div>
                            <div class="col-8">
                                <div id="reader"></div>
                            </div>
                            <div class="col-2">
                            </div>
                        </div>

                        <div class="row">
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='form-label'><small>No Stocker</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty' value = ''
                                        readonly>
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='form-label'><small>WS</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty' value = ''
                                        readonly>
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='form-label'><small>Buyer</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty' value = ''
                                        readonly>
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='form-label'><small>No Cut</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty' value = ''
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='form-label'><small>Style</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty' value = ''
                                        readonly>
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='form-label'><small>Color</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty' value = ''
                                        readonly>
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='form-label'><small>Size</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty'
                                        value = '' readonly>
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='form-label'><small>Part</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty'
                                        value = '' readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class='col-md-6'>
                                <div class='form-group'>
                                    <label class='form-label'><small>Tujuan Asal</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty'
                                        value = '' readonly>
                                </div>
                            </div>
                            <div class='col-md-6'>
                                <div class='form-group'>
                                    <label class='form-label'><small>Lokasi Asal</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty'
                                        value = '' readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='form-label'><small>Qty Awal</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty'
                                        value = '' readonly>
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='form-label'><small>Reject</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty'
                                        value = '' style = 'border-color:blue;'>
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='form-label'><small>Qty In</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty'
                                        value = '' readonly style = 'border-color:green;'>
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label class='form-label'><small>Rak</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty'
                                        value = '' style = 'border-color:blue;'>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class='col-md-12'>
                                <div class='form-group'>
                                    <label class='form-label'><small>Keterangan</small></label>
                                    <input type='text' class='form-control' id='txtqty' name='txtqty'
                                        value = '' style = 'border-color:blue;'>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Simpan </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card card-sb card-outline">
        <div class="card-header">
            <h5 class="card-title fw-bold mb-0">Secondary In</h5>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-end gap-3 mb-3">
                <div class="mb-3">
                    <label class="form-label"><small>Tgl Awal</small></label>
                    <input type="date" class="form-control" id="tgl-awal" name="tgl_awal"
                        value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label"><small>Tgl Akhir</small></label>
                    <input type="date" class="form-control" id="tgl-akhir" name="tgl_akhir"
                        value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalEdit"><i
                            class="fas fa-plus"></i> Baru</button>
                </div>
            </div>
            <h5 class="card-title fw-bold mb-0">List Transaksi In</h5>
            <br>
            <br>
            <div class="table-responsive">
                <table id="datatable" class="table table-bordered table-striped table-sm w-100">
                    <thead>
                        <tr>
                            <th>No Form</th>
                            <th>Tgl Form</th>
                            <th>No. Meja</th>
                            <th>Marker</th>
                            <th>WS</th>
                            <th>Color</th>
                            <th>Panel</th>
                            <th>Size Ratio</th>
                            <th>Total Lembar</th>
                            <th>Act</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('custom-script')
    <!-- DataTables & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $('.select2').select2()
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            dropdownParent: $("#editMejaModal")
        })
    </script>
    <script>
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

        $('.select2').select2()

        // $('.select2bs4').select2({
        //     theme: 'bootstrap4',
        //     dropdownParent: $("#editMejaModal")
        // })

        // Scan QR Module :
        // Variable List :
        var html5QrcodeScanner = null;

        // Function List :
        // -Initialize Scanner-
        async function initScan() {
            if (document.getElementById("reader")) {
                if (html5QrcodeScanner) {
                    await html5QrcodeScanner.clear();
                }

                function onScanSuccess(decodedText, decodedResult) {
                    // handle the scanned code as you like, for example:
                    console.log(`Code matched = ${decodedText}`, decodedResult);

                    // store to input text
                    // let breakDecodedText = decodedText.split('-');

                    document.getElementById('txtqrstocker').value = decodedText;

                    scan_qr();

                    html5QrcodeScanner.clear();
                    initScan();

                }

                function onScanFailure(error) {
                    // handle scan failure, usually better to ignore and keep scanning.
                    // for example:
                    console.warn(`Code scan error = ${error}`);
                }

                html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    },
                    /* verbose= */
                    false);

                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            initScan();
        })
    </script>
@endsection

@extends('layouts.index')

@section('custom-link')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <style type="text/css">
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            /* display: none; <- Crashes Chrome on hover */
            -webkit-appearance: none;
            margin: 0;
            /* <-- Apparently some margin are still there even though it's hidden */
        }

        input[type=number] {
            -moz-appearance: textfield;
            /* Firefox */
        }
    </style>
@endsection

@section('content')
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <form action="{{ route('upload-packing-list') }}" enctype="multipart/form-data" method="post"
            onsubmit="submitForm(this, event)" name='form_upload' id='form_upload'>
            @method('POST')
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-sb text-light">
                        <h3 class="modal-title fs-5"><i class="fas fa-list"></i> Tambah Packing List</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class='row'>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><small><b>Tipe Upload :</b></small></label>
                                    <select class="form-control select2bs4" id="cbotipe" name="cbotipe"
                                        style="width: 100%;" required>
                                        <option selected="selected" value="" disabled="true">Pilih Tipe Upload
                                        </option>
                                        @foreach ($data_list as $datalist)
                                            <option value="{{ $datalist->isi }}">
                                                {{ $datalist->tampil }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><small><b>PO :</b></small></label>
                                    <select class="form-control select2bs4" id="cbopo" name="cbopo"
                                        style="width: 100%;"
                                        onchange="getdatapo();delete_tmp_upload();dataTableUploadReload()" required>
                                        <option selected="selected" value="" disabled="true">Pilih PO</option>
                                        @foreach ($data_po as $datapo)
                                            <option value="{{ $datapo->isi }}">
                                                {{ $datapo->tampil }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="input-group">
                                        <a onclick="export_data_po()"
                                            class="btn btn-outline-success position-relative btn-sm">
                                            <i class="fas fa-file-download fa-sm"></i>
                                            Export Data PO
                                        </a>
                                    </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><small><b>Buyer :</b></small></label>
                                    <input type='texr' id="txtbuyer" name="txtbuyer"
                                        class='form-control form-control-sm' value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><small><b>PO :</b></small></label>
                                    <input type='texr' id="txtpo" name="txtpo" class='form-control form-control-sm'
                                        value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><small><b>Style :</b></small></label>
                                    <input type='texr' id="txtstyle" name="txtstyle"
                                        class='form-control form-control-sm' value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><small><b>Dest :</b></small></label>
                                    <input type='texr' id="txtdest" name="txtdest" class='form-control form-control-sm'
                                        value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class='row'>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><small><b>Upload File</b></small></label>
                                    <input type="file" class="form-control form-control-sm" name="file"
                                        id="file">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="input-group">
                                        <button type="submit" class="btn btn-outline-info btn-sm"><i
                                                class="fas fa-check"></i> Upload
                                        </button>
                                    </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label><small><b>Tidak Terdaftar :</b></small></label>
                                    <input type='texr' id="txtnon_upload" name="txtnon_upload"
                                        class='form-control form-control-sm' value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="datatable_upload"
                                class="table table-bordered table-sm w-100 table-hover display nowrap">
                                <thead class="table-primary">
                                    <tr style='text-align:center; vertical-align:middle'>
                                        <th>Tgl. Shipment</th>
                                        <th>PO #</th>
                                        <th>No. Carton</th>
                                        <th>Tipe Pack</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Qty</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th colspan="5"></th>
                                        <th> <input type = 'text' class="form-control form-control-sm"
                                                style="width:75px" readonly id = 'total_qty_carton'> </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
        </form>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal"><i
                    class="fas fa-times-circle"></i> Tutup</button>
            <a class="btn btn-outline-success btn-sm" onclick="simpan()">
                <i class="fas fa-check"></i>
                Simpan
            </a>
        </div>
    </div>
    </div>

    </div>
    <div class="card card-sb">
        <div class="card-header">
            <h5 class="card-title fw-bold mb-0"><i class="fas fa-list"></i> Packing List</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <a class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"
                    onclick="reset()">
                    <i class="fas fa-plus"></i>
                    Baru
                </a>
            </div>
            <div class="d-flex align-items-end gap-3 mb-3">
                <div class="mb-3">
                    <label class="form-label"><small><b>Tgl Shipment Awal</b></small></label>
                    <input type="date" class="form-control form-control-sm " id="tgl-awal" name="tgl_awal"
                        oninput="dataTableReload()" value="{{ $tgl_awal_fix }}">
                </div>
                <div class="mb-3">
                    <label class="form-label"><small><b>Tgl Shipment Akhir</b></small></label>
                    <input type="date" class="form-control form-control-sm" id="tgl-akhir" name="tgl_akhir"
                        oninput="dataTableReload()" value="{{ $tgl_akhir_fix }}">
                </div>
            </div>
            <div class="table-responsive">
                <table id="datatable" class="table table-bordered table-striped table-sm w-100 text-nowrap">
                    <thead class="table-primary">
                        <tr style='text-align:center; vertical-align:middle'>
                            <th>Tgl. Shipment</th>
                            <th>PO</th>
                            <th>Buyer</th>
                            <th>Tot. Carton</th>
                            <th>Tot. Qty</th>
                        </tr>
                    </thead>
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
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-rowsgroup/dataTables.rowsGroup.js') }}"></script>

    <style>
        .checkbox-xl .form-check-input {
            /* top: 1.2rem; */
            scale: 1.5;
            /* margin-right: 0.8rem; */
        }
    </style>
    <script>
        // Select2 Autofocus
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

        // Initialize Select2 Elements
        $('.select2').select2();

        // Initialize Select2BS4 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4',
        });

        $('#exampleModal').on('show.bs.modal', function(e) {
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                dropdownParent: $("#exampleModal")
            })
            reloadPoData();
        })
    </script>
    <script>
        function notif() {
            alert("Maaf, Fitur belum tersedia!");
        }

        function reset() {
            $("#form_upload").trigger("reset");
            $("#cbotipe").val('HORIZONTAL').trigger('change');
            $("#cbopo").val('').trigger('change');
            dataTableUploadReload();
            document.getElementById('file').value = "";
        }
    </script>
    <script>
        $(document).ready(() => {
            reset();
        });

        function getdatapo() {
            document.getElementById('file').value = "";
            let cbopo = document.form_upload.cbopo.value;
            $.ajax({
                url: '{{ route('show_det_po') }}',
                method: 'get',
                data: {
                    cbopo: cbopo
                },
                dataType: 'json',
                success: function(response) {
                    if (response !== null) {
                        document.getElementById('txtbuyer').value = response.buyer;
                        document.getElementById('txtpo').value = response.po;
                        document.getElementById('txtstyle').value = response.styleno;
                        document.getElementById('txtdest').value = response.dest;
                    } else {
                        document.getElementById('txtbuyer').value = '';
                        document.getElementById('txtpo').value = '';
                        document.getElementById('txtstyle').value = '';
                        document.getElementById('txtdest').value = '';
                    }

                },
                error: function(request, status, error) {
                    alert(request.responseText);
                },
            });
        };

        function export_data_po() {
            let po = $('#cbopo').val();
            let tipe = $('#cbotipe').val();
            if (!tipe) {
                iziToast.warning({
                    message: 'Tipe masih kosong, Silahkan pilih tipe',
                    position: 'topCenter'
                });
            }

            if (!po) {
                iziToast.warning({
                    message: 'PO masih kosong, Silahkan pilih po',
                    position: 'topCenter'
                });
            } else {
                Swal.fire({
                    title: 'Please Wait...',
                    html: 'Exporting Data...',
                    didOpen: () => {
                        Swal.showLoading()
                    },
                    allowOutsideClick: false,
                });

                if (tipe == 'HORIZONTAL') {
                    $.ajax({
                        type: "get",
                        url: '{{ route('export_data_template_po_packing_list_horizontal') }}',
                        data: {
                            po: po
                        },
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(response) {
                            {
                                swal.close();
                                Swal.fire({
                                    title: 'Data Sudah Di Export!',
                                    icon: "success",
                                    showConfirmButton: true,
                                    allowOutsideClick: false
                                });
                                var blob = new Blob([response]);
                                var link = document.createElement('a');
                                link.href = window.URL.createObjectURL(blob);
                                link.download = "PO " + po + "_H.xlsx";
                                link.click();

                            }
                        },
                    });
                } else {
                    $.ajax({
                        type: "get",
                        url: '{{ route('export_data_template_po_packing_list_vertical') }}',
                        data: {
                            po: po
                        },
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(response) {
                            {
                                swal.close();
                                Swal.fire({
                                    title: 'Data Sudah Di Export!',
                                    icon: "success",
                                    showConfirmButton: true,
                                    allowOutsideClick: false
                                });
                                var blob = new Blob([response]);
                                var link = document.createElement('a');
                                link.href = window.URL.createObjectURL(blob);
                                link.download = "PO " + po + "_V.xlsx";
                                link.click();

                            }
                        },
                    });
                }


            }
        }

        function delete_tmp_upload() {
            let po = $('#cbopo').val();
            let html = $.ajax({
                type: "POST",
                url: '{{ route('delete_upload_packing_list') }}',
                data: {
                    po: po
                },
                async: false
            }).responseText;
        }

        function dataTableReload() {
            datatable.ajax.reload();
        }


        $('#datatable thead tr').clone(true).appendTo('#datatable thead');
        $('#datatable thead tr:eq(1) th').each(function(i) {
            var title = $(this).text();
            $(this).html('<input type="text" class="form-control form-control-sm"/>');
            $('input', this).on('keyup change', function() {
                if (datatable.column(i).search() !== this.value) {
                    datatable
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        let datatable = $("#datatable").DataTable({
            ordering: false,
            processing: true,
            serverSide: true,
            paging: false,
            searching: true,
            scrollY: '300px',
            scrollX: '300px',
            scrollCollapse: true,
            ajax: {
                url: '{{ route('packing-list') }}',
                data: function(d) {
                    d.dateFrom = $('#tgl-awal').val();
                    d.dateTo = $('#tgl-akhir').val();
                },
            },
            columns: [{
                    data: 'tgl_shipment_fix'

                },
                {
                    data: 'po'
                },
                {
                    data: 'buyer'
                },
                {
                    data: 'tot_carton'
                },
                {
                    data: 'tot_qty'
                },
            ],
            columnDefs: [{
                "className": "dt-center",
                "targets": "_all"
            }, ]


        }, );



        function dataTableUploadReload() {
            datatable_upload.ajax.reload();
        }

        $('#datatable_upload thead tr').clone(true).appendTo('#datatable_upload thead');
        $('#datatable_upload thead tr:eq(1) th').each(function(i) {
            var title = $(this).text();
            $(this).html('<input type="text" class="form-control form-control-sm"/>');
            $('input', this).on('keyup change', function() {
                if (datatable_upload.column(i).search() !== this.value) {
                    datatable_upload
                        .column(i)
                        .search(this.value)
                        .draw();
                }
            });
        });

        let datatable_upload = $("#datatable_upload").DataTable({
            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                // converting to interger to find total
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                // computing column Total of the complete result
                var sumTotal = api
                    .column(5)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                // Count rows with color red

                var redCount = 0;
                api.rows().every(function() {
                    var row = this.data();
                    if (row.id_ppic_master_so === null) {
                        redCount++;
                    }
                });

                // Update footer by showing the total with the reference of the column index
                $(api.column(0).footer()).html('Total');
                $(api.column(5).footer()).html(sumTotal);
                $('#txtnon_upload').val(redCount);
            },
            ordering: false,
            processing: true,
            serverSide: true,
            paging: false,
            searching: true,
            scrollY: '300px',
            scrollX: '300px',
            scrollCollapse: true,
            ajax: {
                url: '{{ route('show_datatable_upload_packing_list') }}',
                data: function(d) {
                    d.po = $('#cbopo').val();
                    d.tipe = $('#cbotipe').val();
                },
            },
            columns: [{
                    data: 'tgl_shipment_fix'

                },
                {
                    data: 'po'
                },
                {
                    data: 'no_carton'
                },
                {
                    data: 'tipe_pack'
                },
                {
                    data: 'color'
                },
                {
                    data: 'size'
                },
                {
                    data: 'qty'
                },
            ],
            columnDefs: [{
                    "className": "dt-center",
                    "targets": "_all"
                },
                {
                    targets: '_all',
                    className: 'text-nowrap',
                    render: (data, type, row, meta) => {
                        if (row.id_ppic_master_so === null) {
                            color = 'red';
                        } else {
                            color = '#087521';
                        }
                        return '<span style="font-weight: 600; color:' + color + '">' + data + '</span>';
                    }
                },
            ]


        }, );


        function simpan() {
            let po = $('#cbopo').val();
            let txtnon_upload = $('#txtnon_upload').val();
            let tipe = $('#cbotipe').val();
            $.ajax({
                type: "post",
                url: '{{ route('store_upload_packing_list') }}',
                data: {
                    po: po,
                    txtnon_upload: txtnon_upload,
                    tipe: tipe
                },
                success: function(response) {
                    if (response.icon == 'salah') {
                        iziToast.warning({
                            message: response.msg,
                            position: 'topCenter'
                        });
                        dataTableReload();
                        dataTableUploadReload();
                    } else {
                        Swal.fire({
                            text: response.msg,
                            icon: "success"
                        });
                        dataTableReload();
                        dataTableUploadReload();
                        delete_tmp_upload();
                    }

                },
                error: function(request, status, error) {
                    iziToast.warning({
                        message: 'Silahkan cek lagi',
                        position: 'topCenter'
                    });
                    dataTableUploadReload();
                    delete_tmp_upload();
                    dataTableReload();
                },
            });

        };

        function reloadPoData() {
            const cbopo = $('#cbopo');
            cbopo.empty(); // Clear existing options
            cbopo.append('<option selected="selected" value="" disabled="true">Pilih PO</option>'); // Default option
            $.ajax({
                type: 'GET',
                url: '{{ route('getPoData') }}',
                success: function(data) {
                    console.log('Received data:', data); // Debugging statement
                    $.each(data, function(index, item) {
                        cbopo.append($('<option>', {
                            value: item.isi,
                            text: item.tampil
                        }));
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching PO data:', error); // Debugging statement
                }
            });
        }
    </script>
@endsection

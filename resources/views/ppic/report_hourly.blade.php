@extends('layouts.index')

@section('custom-link')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link href="https://cdn.datatables.net/2.0.3/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedcolumns/5.0.0/css/fixedColumns.bootstrap4.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="card card-sb">
        <div class="card-header">
            <h5 class="card-title fw-bold mb-0"><i class="fas fa-chart-area"></i> Summary Transaksi</h5>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-end gap-3 mb-3">
                <div class="mb-3">
                    <a onclick="dataTableReload()" class="btn btn-outline-primary position-relative btn-sm">
                        <i class="fas fa-search fa-sm"></i>
                    </a>
                </div>
                <div class="mb-3">
                    <a onclick="export_excel_tracking()" class="btn btn-outline-success position-relative btn-sm">
                        <i class="fas fa-file-excel fa-sm"></i>
                        Export Excel
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table id="datatable" class="table table-bordered table-striped table-sm w-100 text-nowrap">
                    <thead class="table-primary">
                        <tr style='text-align:center; vertical-align:middle'>
                            <th>Line</th>
                            <th>Style</th>
                            <th>Jumlah OP</th>
                            <th>SMV</th>
                            <th>Kemarin 1</th>
                            <th>Kemarin 2</th>
                            <th>Target Eff</th>
                            <th>PCS</th>
                            <th>100 % Target</th>
                            <th>100% Target / Jam</th>
                            <th>Jumlah Hari</th>
                            <th>Perjam</th>
                            <th>Perhari</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>Total</th>
                            <th>Earned Minutes</th>
                            <th>Eff</th>
                        </tr>
                    </thead>
                    <tbody>

                        <!-- Data will be populated here by DataTables -->

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('custom-script')
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/5.0.0/js/dataTables.fixedColumns.min.js"></script>

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
            containerCssClass: 'form-control-sm rounded'
        });
    </script>
    <script>
        $(document).ready(() => {
            dataTableReload();
        });

        function notif() {
            alert("Maaf, Fitur belum tersedia!");
        }

        let datatable = $("#datatable").DataTable({
            scrollY: "500px",
            scrollX: true,
            scrollCollapse: true,
            paging: false,
            ordering: false,
            fixedColumns: {
                leftColumns: 2 // Fix the first two columns
            },
            ajax: {
                url: '{{ route('report-hourly') }}',
                dataType: 'json',
                dataSrc: 'data',
                // data: function(d) {
                //     d.buyer = $('#cbobuyer').val();
                // },
            },
            columns: [{
                    data: 'sewing_line'

                },
                {
                    data: 'styleno'
                },
                {
                    data: 'man_power'
                },
                {
                    data: 'smv'
                },
                {
                    data: 'kemarin_1'
                },
                {
                    data: 'kemarin_2'
                },
                {
                    data: 'target_effy'
                },
                {
                    data: 'pcs'
                },
                {
                    data: 'target_100'
                },
                {
                    data: 'target_100_per_jam'
                },
                {
                    data: 'tot_days'
                },
                {
                    data: 'perjam'
                },
                {
                    data: 'perhari'
                },
                {
                    data: 'jam_1'
                },
                {
                    data: 'jam_2'
                },
                {
                    data: 'jam_3'
                },
                {
                    data: 'jam_4'
                },
                {
                    data: 'jam_5'
                },
                {
                    data: 'jam_6'
                },
                {
                    data: 'jam_7'
                },
                {
                    data: 'jam_8'
                },
                {
                    data: 'jam_9'
                },
                {
                    data: 'jam_10'
                },
                {
                    data: 'jam_11'
                },
                {
                    data: 'jam_12'
                },
                {
                    data: 'jam_13'
                },
                {
                    data: 'tot_input'
                },
                {
                    data: 'earned_minutes'
                },
                {
                    data: 'eff'
                },
            ],
            columnDefs: [{
                "className": "align-left",
                "targets": "_all"
            }, ],

        });


        function dataTableReload() {
            datatable.ajax.reload();
        }

        function export_excel_tracking() {
            let buyer = document.getElementById("cbobuyer").value;
            Swal.fire({
                title: 'Please Wait...',
                html: 'Exporting Data...',
                didOpen: () => {
                    Swal.showLoading()
                },
                allowOutsideClick: false,
            });

            $.ajax({
                type: "get",
                url: '{{ route('export_excel_tracking') }}',
                data: {
                    buyer: buyer
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
                        link.download = "Laporan Tracking " + buyer + ".xlsx";
                        link.click();

                    }
                },
            });
        }
    </script>
@endsection

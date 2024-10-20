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
    <form action="{{ route('export_excel') }}" method="get">
        <div class="card card-sb">
            <div class="card-header">
                <h5 class="card-title fw-bold mb-0"><i class="fa-solid fa-toilet-paper fa-sm"></i> Pemakaian Roll</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-end gap-3">
                    <div class="d-flex align-items-end gap-3 mb-3">
                        <div class="mb-3">
                            <label class="form-label"><small>Tanggal Awal</small></label>
                            <input type="date" class="form-control form-control-sm" id="from" name="from" value="{{ date('Y-m-d') }}" onchange="datatableReload()">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><small>Tanggal Akhir</small></label>
                            <input type="date" class="form-control form-control-sm" id="to" name="to" value="{{ date('Y-m-d') }}" onchange="datatableReload()">
                        </div>
                        <button type="button" class="btn btn-primary btn-sm mb-3"><i class="fa fa-search fa-sm"></i></button>
                    </div>
                    <div class="d-flex align-items-end gap-3 mb-3">
                        <div class="mb-3">
                            <button type='submit' name='submit' class='btn btn-success btn-sm'>
                                <i class="fas fa-file-excel"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered table-hover table-sm w-100">
                        <thead>
                            <tr>
                                <th>Bulan</th>
                                <th>Tanggal Input</th>
                                <th>No. Form</th>
                                <th>Meja</th>
                                <th>No. WS</th>
                                <th>Buyer</th>
                                <th>Style</th>
                                <th>Color</th>
                                <th>Color Actual</th>
                                <th>Panel</th>
                                <th>Qty Order</th>
                                <th>Cons. WS</th>
                                <th>Cons. Marker</th>
                                <th>Cons. Ampar</th>
                                <th>Cons. Actual</th>
                                <th>Cons. Piping</th>
                                <th>Panjang Marker</th>
                                <th>Unit Panjang Marker</th>
                                <th>Comma Marker</th>
                                <th>Unit Comma Marker</th>
                                <th>Lebar Marker</th>
                                <th>Unit Lebar Marker</th>
                                <th>Panjang Actual</th>
                                <th>Unit Panjang Actual</th>
                                <th>Comma Actual</th>
                                <th>Unit Comma Actual</th>
                                <th>Lebar Actual</th>
                                <th>Unit Lebar Actual</th>
                                <th>ID Roll</th>
                                <th>ID Item</th>
                                <th>Detail Item</th>
                                <th>No. Roll</th>
                                <th>Lot</th>
                                <th>Group</th>
                                <th>Qty Roll</th>
                                <th>Unit Roll</th>
                                <th>Berat Amparan (KGM)</th>
                                <th>Estimasi Amparan</th>
                                <th>Lembar Amparan</th>
                                <th>Ratio</th>
                                <th>Qty Cut</th>
                                <th>Average Time</th>
                                <th>Sisa Gelaran</th>
                                <th>Sambungan</th>
                                <th>Sambungan Roll</th>
                                <th>Kepala Kain</th>
                                <th>Sisa Tidak Bisa</th>
                                <th>Reject</th>
                                <th>Piping</th>
                                <th>Sisa Kain</th>
                                <th>Pemakaian Gelar</th>
                                <th>Total Pemakaian Roll</th>
                                <th>Short Roll</th>
                                <th>Short Roll (%)</th>
                                <th>Operator</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('custom-script')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables-rowsgroup/dataTables.rowsGroup.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let oneWeeksBefore = new Date(new Date().setDate(new Date().getDate() - 7));
            let oneWeeksBeforeDate = ("0" + oneWeeksBefore.getDate()).slice(-2);
            let oneWeeksBeforeMonth = ("0" + (oneWeeksBefore.getMonth() + 1)).slice(-2);
            let oneWeeksBeforeYear = oneWeeksBefore.getFullYear();
            let oneWeeksBeforeFull = oneWeeksBeforeYear + '-' + oneWeeksBeforeMonth + '-' + oneWeeksBeforeDate;

            // $("#from").val(oneWeeksBeforeFull).trigger("change");

            // window.addEventListener("focus", () => {
            //     datatableReload();
            // });
        });

        $('#datatable thead tr').clone(true).appendTo('#datatable thead');
        $('#datatable thead tr:eq(1) th').each(function(i) {
            if (i <= 9 || i == 17 || i == 21 || i == 23 || i == 25 || i == 26 || i == 27 || i == 28 || i == 29 || i == 30 || i == 31) {
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control form-control-sm" />');

                $('input', this).on('keyup change', function() {
                    if (datatable.column(i).search() !== this.value) {
                        datatable
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            } else {
                $(this).empty();
            }
        });

        let datatable = $("#datatable").DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            scrollX: "500px",
            scrollY: "500px",
            pageLength: 50,
            ajax: {
                url: '{{ route('lap_pemakaian_data') }}',
                method: "POST",
                data: function(d) {
                    d.dateFrom = $('#from').val();
                    d.dateTo = $('#to').val();
                },
            },
            columns: [
                {
                    data: "bulan"
                },
                {
                    data: "tgl_input"
                },
                {
                    data: "no_form_cut_input"
                },
                {
                    data: "nama_meja"
                },
                {
                    data: "act_costing_ws"
                },
                {
                    data: "buyer"
                },
                {
                    data: "style"
                },
                {
                    data: "color"
                },
                {
                    data: "color_act"
                },
                {
                    data: "panel"
                },
                {
                    data: "qty"
                },
                {
                    data: "cons_ws"
                },
                {
                    data: "cons_marker"
                },
                {
                    data: "cons_ampar"
                },
                {
                    data: "cons_act"
                },
                {
                    data: "cons_piping"
                },
                {
                    data: "panjang_marker"
                },
                {
                    data: "unit_panjang_marker"
                },
                {
                    data: "comma_marker"
                },
                {
                    data: "unit_comma_marker"
                },
                {
                    data: "lebar_marker"
                },
                {
                    data: "unit_lebar_marker"
                },
                {
                    data: "panjang_actual"
                },
                {
                    data: "unit_panjang_actual"
                },
                {
                    data: "comma_actual"
                },
                {
                    data: "unit_comma_actual"
                },
                {
                    data: "lebar_actual"
                },
                {
                    data: "unit_lebar_actual"
                },
                {
                    data: "id_roll"
                },
                {
                    data: "id_item"
                },
                {
                    data: "detail_item"
                },
                {
                    data: "roll"
                },
                {
                    data: "lot"
                },
                {
                    data: "group_roll"
                },
                {
                    data: "qty_roll"
                },
                {
                    data: "unit_roll"
                },
                {
                    data: "berat_amparan"
                },
                {
                    data: "est_amparan"
                },
                {
                    data: "lembar_gelaran"
                },
                {
                    data: "total_ratio"
                },
                {
                    data: "qty_cut"
                },
                {
                    data: "average_time"
                },
                {
                    data: "sisa_gelaran"
                },
                {
                    data: "sambungan"
                },
                {
                    data: "sambungan_roll"
                },
                {
                    data: "kepala_kain"
                },
                {
                    data: "sisa_tidak_bisa"
                },
                {
                    data: "reject"
                },
                {
                    data: "piping"
                },
                {
                    data: "sisa_kain"
                },
                {
                    data: "pemakaian_lembar"
                },
                {
                    data: "total_pemakaian_roll"
                },
                {
                    data: "short_roll"
                },
                {
                    data: "short_roll_percentage"
                },
                {
                    data: "operator"
                }
            ],
            columnDefs: [
                {
                    targets: "_all",
                    className: "text-nowrap"
                }
            ],
        });

        function datatableReload() {
            datatable.ajax.reload();
        }
    </script>
@endsection

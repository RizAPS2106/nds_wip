<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Export_excel_rep_packing_line_sum_range;
use App\Exports\Export_excel_rep_packing_line_sum_buyer;
use App\Exports\export_excel_rep_packing_mutasi;
use App\Exports\ExportDataTemplatePackingListVertical;


class PackingReportController extends Controller
{
    public function packing_rep_packing_line_sum(Request $request)
    {
        $tgl_akhir_fix = date('Y-m-d', strtotime("+90 days"));
        $tgl_awal_fix = date('Y-m-d', strtotime("-90 days"));
        $tgl_skrg = date('Y-m-d');
        $user = Auth::user()->name;

        $data_tipe = DB::select("SELECT 'RANGE' isi , 'RANGE' tampil
        UNION
        SELECT 'BUYER' isi , 'BUYER' tampil
        ");

        $data_po = DB::select("SELECT buyer isi, buyer tampil from ppic_master_so p
        inner join master_sb_ws m on  p.id_so_det = m.id_so_det
        group by buyer
        order by buyer asc
        ");



        return view(
            'packing.packing_rep_packing_line',
            [
                'page' => 'dashboard-packing',
                "subPageGroup" => "packing-report",
                "subPage" => "packing_rep_packing_line_sum",
                "data_tipe" => $data_tipe,
                "data_po" => $data_po,
                "user" => $user,
                "tgl_skrg" => $tgl_skrg,
                "tgl_awal_fix" => $tgl_awal_fix,
                "tgl_akhir_fix" => $tgl_akhir_fix,
            ]
        );
    }

    public function packing_rep_packing_line_sum_range(Request $request)
    {
        $user = Auth::user()->name;
        $tgl_awal = $request->dateFrom;
        $tgl_akhir = $request->dateTo;
        if ($request->ajax()) {
            $data_pl = DB::select("SELECT
                UPPER(REPLACE(a.created_by, '_', ' ')) sew_line,
                a.created_by,
                m.buyer,
                m.ws,
                m.color,
                m.size,
                a.qty
                from
                (
                select
                so_det_id,
                count(so_det_id) qty,
                created_by
                from output_rfts_packing where date(updated_at) >= '$tgl_awal' and date(updated_at) <= '$tgl_akhir'
                group by so_det_id, created_by
                ) a
                inner join master_sb_ws m on a.so_det_id = m.id_so_det
                left join master_size_new msn on m.size = msn.size
                where created_by is not null
                order by a.created_by asc,ws asc, color asc, urutan asc
              ");

            return DataTables::of($data_pl)->toJson();
        }
    }

    public function packing_rep_packing_line_sum_buyer(Request $request)
    {
        $user = Auth::user()->name;
        $buyer = $request->cbobuyer;

        if ($request->ajax()) {
            $data_pl = DB::select("SELECT
			buyer,
            so_det_id,
			ws,
			color,
			b.size,
            count(so_det_id) qty
            from output_rfts_packing a
			inner join
			(
			select buyer,p.id_so_det, ws, color, size from ppic_master_so p
			inner join master_sb_ws m on p.id_so_det = m.id_so_det
			where m.buyer = '$buyer'
			) b on a.so_det_id = b.id_so_det
		    inner join master_size_new msn on b.size = msn.size
             group by so_det_id
			order by ws asc, color asc, urutan asc
              ");

            return DataTables::of($data_pl)->toJson();
        }
    }

    public function export_excel_rep_packing_line_sum_range(Request $request)
    {
        return Excel::download(new Export_excel_rep_packing_line_sum_range($request->from, $request->to), 'Laporan_Packing_In.xlsx');
    }

    public function export_excel_rep_packing_line_sum_buyer(Request $request)
    {
        return Excel::download(new Export_excel_rep_packing_line_sum_buyer($request->buyer), 'Laporan_Packing_In.xlsx');
    }


    public function packing_rep_packing_mutasi(Request $request)
    {
        return view(
            'packing.packing_rep_packing_mutasi',
            [
                'page' => 'dashboard-packing',
                "subPageGroup" => "packing-report",
                "subPage" => "packing_rep_packing_mutasi",
            ]
        );
    }

    public function packing_rep_packing_mutasi_load(Request $request)
    {

        // if ($request->ajax()) {
        $data_mut = DB::select("SELECT p.po, m.buyer, m.ws, m.color, m.size, p.dest,a.barcode, a.no_carton,a.qty qty_pl,
        coalesce(b.tot_scan,0) tot_scan, coalesce(c.qty_fg_in,0) qty_fg_in, coalesce(qty_fg_out,0) qty_fg_out , lokasi, coalesce(a.qty,0) - coalesce(qty_fg_out,0) balance
from packing_master_packing_list a
left join
	(
	select count(barcode) tot_scan, po, barcode, no_carton from packing_packing_out_scan
	group by po, barcode, no_carton
	) b on a.barcode = b.barcode and a.po = b.po and a.no_carton = b.no_carton
left join
	(
	select sum(qty) qty_fg_in, po, barcode, no_carton, lokasi from fg_fg_in where status = 'NORMAL' group by po, barcode, no_carton
	) c on a.barcode = c.barcode and a.po = c.po and a.no_carton = c.no_carton
left join
	(
	select sum(qty) qty_fg_out, po, barcode, no_carton from fg_fg_out where status = 'NORMAL' group by po, barcode, no_carton
	) d on a.barcode = d.barcode and a.po = d.po and a.no_carton = d.no_carton
inner join ppic_master_so p on a.id_ppic_master_so = p.id
inner join master_sb_ws m on p.id_so_det = m.id_so_det
left join master_size_new msn on m.size = msn.size
order by a.po asc, buyer asc, no_carton asc, urutan asc

      ");

        return DataTables::of($data_mut)->toJson();
        // }
    }


    public function export_excel_rep_packing_mutasi(Request $request)
    {
        return Excel::download(new export_excel_rep_packing_mutasi, 'Laporan_Packing_In.xlsx');
    }
}

<?php

namespace App\Http\Controllers\Cutting;

use App\Http\Controllers\Controller;
use App\Models\FormCutInputDetail;
use App\Exports\ExportReportCutting;
use App\Exports\ExportReportCuttingSinglePage;
use App\Exports\ExportPemakaianKain;
use App\Exports\ExportDetailPemakaianKain;
use App\Exports\ExportReportCuttingDaily;
use App\Exports\Cutting\CuttingOrderOutputExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use DB;

class ReportCuttingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cutting(Request $request)
    {
        if ($request->ajax()) {
            $additionalQuery = "";

            if ($request->dateFrom) {
                $additionalQuery .= " and COALESCE(DATE(form_cut_input.waktu_selesai), DATE(form_cut_input.waktu_mulai), DATE(form_cut_input.tgl_input)) >= '".$request->dateFrom."'";
            }

            if ($request->dateTo) {
                $additionalQuery .= " and COALESCE(DATE(form_cut_input.waktu_selesai), DATE(form_cut_input.waktu_mulai), DATE(form_cut_input.tgl_input)) <= '".$request->dateTo."'";
            }

            $keywordQuery = "";
            if ($request->search["value"]) {
                $keywordQuery = "
                    and (
                        marker_cutting.tgl_form_cut like '%" . $request->search["value"] . "%' OR
                        marker_cutting.meja like '%" . $request->search["value"] . "%' OR
                        marker_cutting.buyer like '%" . $request->search["value"] . "%' OR
                        marker_cutting.act_costing_ws like '%" . $request->search["value"] . "%' OR
                        marker_cutting.style like '%" . $request->search["value"] . "%' OR
                        marker_cutting.color like '%" . $request->search["value"] . "%' OR
                        marker_cutting.notes like '%" . $request->search["value"] . "%'
                    )
                ";
            }

            $reportCutting = DB::select("
                SELECT
                    marker_cutting.tgl_form_cut,
                    marker_cutting.meja,
                    marker_cutting.buyer,
                    marker_cutting.act_costing_ws,
                    marker_cutting.style,
                    marker_cutting.color,
                    marker_cutting.panel,
                    marker_cutting.cons_ws,
                    marker_cutting.unit,
                    marker_cutting.so_det_id,
                    marker_cutting.size,
                    COALESCE(marker_cutting.notes, '-') notes,
                    SUM(marker_cutting.marker_gelar * marker_cutting.ratio) marker_gelar,
                    SUM(marker_cutting.spreading_gelar  * marker_cutting.ratio) spreading_gelar,
                    SUM((marker_cutting.form_gelar * marker_cutting.ratio) + COALESCE(marker_cutting.diff, 0)) form_gelar,
                    SUM(COALESCE(marker_cutting.diff, 0)) form_diff
                FROM
                    (
                        SELECT
                            marker_input.kode,
                            form_cut.no_form,
                            form_cut.meja,
                            form_cut.tgl_form_cut,
                            marker_input.buyer,
                            marker_input.act_costing_id,
                            marker_input.act_costing_ws,
                            marker_input.style,
                            marker_input.color,
                            marker_input.panel,
                            marker_input.cons_ws,
                            marker_input.unit_panjang_marker unit,
                            marker_input_detail.so_det_id,
                            CONCAT(master_sb_ws.size, CASE WHEN master_sb_ws.dest != '-' AND master_sb_ws.dest IS NOT NULL THEN CONCAT(' - ', master_sb_ws.dest) ELSE '' END) size,
                            marker_input_detail.ratio,
                            COALESCE(marker_input.notes, form_cut.notes) notes,
                            marker_input.gelar_qty marker_gelar,
                            SUM(form_cut.qty_ply) spreading_gelar,
                            SUM(COALESCE(form_cut.total_lembar, form_cut.detail)) form_gelar,
                            SUM(modify_size_qty.difference_qty) diff
                        FROM
                            marker_input
                            INNER JOIN
                                marker_input_detail on marker_input_detail.marker_id = marker_input.id
                            INNER JOIN
                                master_sb_ws on master_sb_ws.id_so_det = marker_input_detail.so_det_id
                            INNER JOIN
                                (
                                    SELECT
                                        meja.`name` meja,
                                        COALESCE(DATE(form_cut_input.waktu_selesai), DATE(form_cut_input.waktu_mulai), DATE(form_cut_input.tgl_input)) tgl_form_cut,
                                        form_cut_input.id_marker,
                                        form_cut_input.no_form,
                                        form_cut_input.qty_ply,
                                        form_cut_input.total_lembar,
                                        form_cut_input.notes,
                                        SUM(form_cut_input_detail.lembar_gelaran) detail
                                    FROM
                                        form_cut_input
                                        LEFT JOIN users meja ON meja.id = form_cut_input.no_meja
                                        INNER JOIN form_cut_input_detail ON form_cut_input_detail.no_form_cut_input = form_cut_input.no_form
                                    WHERE
                                        form_cut_input.`status` = 'SELESAI PENGERJAAN'
                                        AND form_cut_input.waktu_mulai is not null
                                        ".$additionalQuery."
                                    GROUP BY
                                        form_cut_input.no_form
                                ) form_cut on form_cut.id_marker = marker_input.kode
                            LEFT JOIN
                                modify_size_qty ON modify_size_qty.no_form = form_cut.no_form AND modify_size_qty.so_det_id = marker_input_detail.so_det_id
                            where
                                (marker_input.cancel IS NULL OR marker_input.cancel != 'Y')
                                AND marker_input_detail.ratio > 0
                            group by
                                marker_input.id,
                                marker_input_detail.so_det_id,
                                form_cut.tgl_form_cut
                    ) marker_cutting
                GROUP BY
                    marker_cutting.act_costing_id,
                    marker_cutting.color,
                    marker_cutting.panel,
                    marker_cutting.so_det_id,
                    marker_cutting.tgl_form_cut
                ORDER BY
                    marker_cutting.panel,
                    marker_cutting.act_costing_id,
                    marker_cutting.color,
                    marker_cutting.so_det_id,
                    marker_cutting.tgl_form_cut
                ");

            return DataTables::of($reportCutting)->toJson();
        }

        return view('cutting.report.report-cutting', ['page' => 'dashboard-cutting', "subPageGroup" => "cutting-report", "subPage" => "cutting"]);
    }

    public function totalCutting(Request $request)
    {
        $additionalQuery = "";
        $additionalQuery1 = "";

        if ($request->dateFrom) {
            $additionalQuery .= " and COALESCE(DATE(form_cut_input.waktu_selesai), DATE(form_cut_input.waktu_mulai), DATE(form_cut_input.tgl_input)) >= '".$request->dateFrom."'";
        }

        if ($request->dateTo) {
            $additionalQuery .= " and COALESCE(DATE(form_cut_input.waktu_selesai), DATE(form_cut_input.waktu_mulai), DATE(form_cut_input.tgl_input)) <= '".$request->dateTo."'";
        }

        if ($request->tgl_form_cut) {
            $additionalQuery .= " and COALESCE(DATE(form_cut_input.waktu_selesai), DATE(form_cut_input.waktu_mulai), DATE(form_cut_input.tgl_input)) LIKE '%".$request->tgl_form_cut."%'";
        }

        if ($request->buyer) {
            $additionalQuery1 .= " and marker_input.buyer LIKE '%".$request->buyer."%'";
        }

        if ($request->ws) {
            $additionalQuery1 .= " and marker_input.act_costing_ws LIKE '%".$request->ws."%'";
        }

        if ($request->style) {
            $additionalQuery1 .= " and marker_input.style LIKE '%".$request->style."%'";
        }

        if ($request->color) {
            $additionalQuery1 .= " and marker_input.color LIKE '%".$request->color."%'";
        }

        if ($request->panel) {
            $additionalQuery1 .= " and marker_input.panel LIKE '%".$request->panel."%'";
        }

        if ($request->size) {
            $additionalQuery1 .= " and marker_input_detail.buyer LIKE '%".$request->size."%'";
        }

        if ($request->notes) {
            $additionalQuery1 .= " and (form_cut.notes LIKE '%".$request->notes."%' or marker_input.notes LIKE '%".$request->notes."%')";
        }

        $reportCutting = DB::select("
            SELECT
                SUM(marker_cutting.marker_gelar * marker_cutting.ratio) marker_gelar,
                SUM(marker_cutting.spreading_gelar  * marker_cutting.ratio) spreading_gelar,
                SUM((marker_cutting.form_gelar * marker_cutting.ratio) + COALESCE(marker_cutting.diff, 0)) form_gelar,
                SUM(COALESCE(marker_cutting.diff, 0)) form_diff
            FROM
                (
                    SELECT
                        marker_input.kode,
                        form_cut.no_form,
                        form_cut.meja,
                        form_cut.tgl_form_cut,
                        marker_input.buyer,
                        marker_input.act_costing_id,
                        marker_input.act_costing_ws,
                        marker_input.style,
                        marker_input.color,
                        marker_input.panel,
                        marker_input.cons_ws,
                        marker_input.unit_panjang_marker unit,
                        marker_input_detail.so_det_id,
                        CONCAT(master_sb_ws.size, CASE WHEN master_sb_ws.dest != '-' AND master_sb_ws.dest IS NOT NULL THEN CONCAT(' - ', master_sb_ws.dest) ELSE '' END) size,
                        marker_input_detail.ratio,
                        COALESCE(marker_input.notes, form_cut.notes) notes,
                        marker_input.gelar_qty marker_gelar,
                        SUM(form_cut.qty_ply) spreading_gelar,
                        SUM(COALESCE(form_cut.total_lembar, form_cut.detail)) form_gelar,
                        SUM(modify_size_qty.difference_qty) diff
                    FROM
                        marker_input
                        INNER JOIN
                            marker_input_detail on marker_input_detail.marker_id = marker_input.id
                        INNER JOIN
                            master_sb_ws on master_sb_ws.id_so_det = marker_input_detail.so_det_id
                        INNER JOIN
                            (
                                SELECT
                                    meja.`name` meja,
                                    COALESCE(DATE(form_cut_input.waktu_selesai), DATE(form_cut_input.waktu_mulai), DATE(form_cut_input.tgl_input)) tgl_form_cut,
                                    form_cut_input.id_marker,
                                    form_cut_input.no_form,
                                    form_cut_input.qty_ply,
                                    form_cut_input.total_lembar,
                                    form_cut_input.notes,
                                    SUM(form_cut_input_detail.lembar_gelaran) detail
                                FROM
                                    form_cut_input
                                    LEFT JOIN users meja ON meja.id = form_cut_input.no_meja
                                    INNER JOIN form_cut_input_detail ON form_cut_input_detail.no_form_cut_input = form_cut_input.no_form
                                WHERE
                                    form_cut_input.`status` = 'SELESAI PENGERJAAN'
                                    AND form_cut_input.waktu_mulai is not null
                                    ".$additionalQuery."
                                GROUP BY
                                    form_cut_input.no_form
                            ) form_cut on form_cut.id_marker = marker_input.kode
                        LEFT JOIN
                            modify_size_qty ON modify_size_qty.no_form = form_cut.no_form AND modify_size_qty.so_det_id = marker_input_detail.so_det_id
                        where
                            (marker_input.cancel IS NULL OR marker_input.cancel != 'Y')
                            AND marker_input_detail.ratio > 0
                            ".$additionalQuery1."
                        group by
                            marker_input.id,
                            marker_input_detail.so_det_id,
                            form_cut.tgl_form_cut
                ) marker_cutting
            ");

        return $reportCutting;
    }

    public function pemakaianRoll(Request $request) {
        ini_set("max_execution_time", 36000);

        if ($request->ajax()) {
            $dateFrom = $request->dateFrom ? $request->dateFrom : date('Y-m-d');
            $dateTo = $request->dateTo ? $request->dateTo : date('Y-m-d');

            $pemakaianRoll = DB::connection("mysql_sb")->select("
                select a.*,b.no_bppb no_out, COALESCE(total_roll,0) roll_out, ROUND(COALESCE(qty_out,0), 2) qty_out, c.no_dok no_retur, COALESCE(total_roll_ri,0) roll_retur, ROUND(COALESCE(qty_out_ri,0), 2) qty_retur from (select bppbno,bppbdate,s.supplier tujuan,ac.kpno no_ws,ac.styleno,ms.supplier buyer,a.id_item,
                REPLACE(mi.itemdesc, '\"', '\\\\\"') itemdesc,a.qty qty_req,a.unit
                from bppb_req a inner join mastersupplier s on a.id_supplier=s.id_supplier
                inner join jo_det jod on a.id_jo=jod.id_jo
                inner join so on jod.id_so=so.id
                inner join act_costing ac on so.id_cost=ac.id
                inner join mastersupplier ms on ac.id_buyer=ms.id_supplier
                inner join masteritem mi on a.id_item=mi.id_item
                where bppbno like '%RQ-F%' and a.id_supplier = '432' and bppbdate between '".$dateFrom."' and '".$dateTo."'
                group by a.id_item,a.bppbno
                order by bppbdate,bppbno desc) a left join
                (select a.no_bppb,no_req,id_item,COUNT(id_roll) total_roll, sum(qty_out) qty_out,satuan from whs_bppb_h a INNER JOIN (select bppbno,bppbdate from bppb_req where bppbno like '%RQ-F%' and id_supplier = '432' and bppbdate between '".$dateFrom."' and '".$dateTo."' GROUP BY bppbno) b on b.bppbno = a.no_req inner join whs_bppb_det c on c.no_bppb = a.no_bppb where a.status != 'Cancel' and c.status = 'Y' GROUP BY a.no_bppb,no_req,id_item) b on b.no_req = a.bppbno and b.id_item = a.id_item left join
                (select a.no_dok, no_invoice no_req,id_item,COUNT(no_barcode) total_roll_ri, sum(qty_sj) qty_out_ri,satuan from (select * from whs_inmaterial_fabric where no_dok like '%RI%' and supplier = 'Production - Cutting' ) a INNER JOIN (select bppbno,bppbdate from bppb_req where bppbno like '%RQ-F%' and id_supplier = '432' and bppbdate between '".$dateFrom."' and '".$dateTo."' GROUP BY bppbno) b on b.bppbno = a.no_invoice INNER JOIN whs_lokasi_inmaterial c on c.no_dok = a.no_dok GROUP BY a.no_dok,no_invoice,id_item) c on c.no_req = a.bppbno and c.id_item  =a.id_item
            ");

            // $cutting = collect(
            //     DB::select("
            //         SELECT
            //             a.no_bppb,
            //             a.no_req,
            //             cutting.id_item,
            //             sum( qty_out ) qty_out,
            //             COUNT( cutting.id_roll ) total_roll,
            //             ROUND ( (CASE WHEN satuan = 'YRD' OR satuan = 'YARD' THEN sum( cutting.total_qty ) * 1.09361 ELSE sum( cutting.total_qty ) END ) , 2) total_qty_roll,
            //             ROUND ( (CASE WHEN satuan = 'YRD' OR satuan = 'YARD' THEN sum( cutting.total_pemakaian_roll ) * 1.09361 ELSE sum( cutting.total_pemakaian_roll ) END ) , 2) total_pakai_roll,
            //             cutting.satuan
            //         FROM
            //             whs_bppb_h a
            //             INNER JOIN ( SELECT bppbno, bppbdate FROM bppb_req WHERE bppbno LIKE '%RQ-F%' AND id_supplier = '432' AND bppbdate between '".$dateFrom."' and '".$dateTo."'  GROUP BY bppbno ) b ON b.bppbno = a.no_req
            //             INNER JOIN ( select whs_bppb_det.id_roll, whs_bppb_det.id_item, whs_bppb_det.no_bppb, whs_bppb_det.satuan, whs_bppb_det.qty_out, COUNT(form_cut_input_detail.id) total_roll, MAX(CAST(form_cut_input_detail.qty as decimal(11,3))) total_qty, SUM(form_cut_input_detail.total_pemakaian_roll) total_pemakaian_roll from whs_bppb_det inner join form_cut_input_detail on form_cut_input_detail.id_roll = whs_bppb_det.id_roll group by whs_bppb_det.id_roll ) as cutting on cutting.no_bppb = a.no_bppb
            //         WHERE
            //             a.STATUS != 'Cancel'
            //         GROUP BY
            //             a.no_bppb,
            //             no_req,
            //             id_item
            //     ")
            // );

            return DataTables::of($pemakaianRoll)->
                addColumn('total_roll_cutting', function ($row) {
                    $rollIdsArr = collect(DB::connection("mysql_sb")->select("select id_roll from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb WHERE a.no_req = '".$row->bppbno."' and b.id_item = '".$row->id_item."' and b.status = 'Y' GROUP BY id_roll"));

                    $rollIds = $rollIdsArr->pluck("id_roll");

                    $rolls = FormCutInputDetail::selectRaw("
                            id_roll,
                            id_item,
                            detail_item,
                            lot,
                            COALESCE(roll_buyer, roll) roll,
                            MAX(qty) qty,
                            unit,
                            ROUND(SUM(total_pemakaian_roll), 2) total_pemakaian_roll,
                            ROUND(SUM(CASE WHEN short_roll < 0 THEN short_roll ELSE 0 END), 2) total_short_roll
                        ")->
                        whereNotNull("id_roll")->
                        whereIn("id_roll", $rollIds)->
                        groupBy("id_item", "id_roll")->
                        get();

                    return $rolls->count();
                })->
                addColumn('total_roll_balance', function ($row) {
                    $rollIdsArr = collect(DB::connection("mysql_sb")->select("select id_roll from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb WHERE a.no_req = '".$row->bppbno."' and b.id_item = '".$row->id_item."' and b.status = 'Y' GROUP BY id_roll"));

                    $rollIds = $rollIdsArr->pluck("id_roll");

                    $rolls = FormCutInputDetail::selectRaw("
                            id_roll,
                            id_item,
                            detail_item,
                            lot,
                            COALESCE(roll_buyer, roll) roll,
                            MAX(qty) qty,
                            unit,
                            ROUND(SUM(total_pemakaian_roll), 2) total_pemakaian_roll,
                            ROUND(SUM(CASE WHEN short_roll < 0 THEN short_roll ELSE 0 END), 2) total_short_roll
                        ")->
                        whereNotNull("id_roll")->
                        whereIn("id_roll", $rollIds)->
                        groupBy("id_item", "id_roll")->
                        get();

                    $balance = $rolls ? $row->roll_out - $rolls->count() : $row->roll_out;

                    return $balance > 0 ? $balance : ($balance < 0 ? str_replace("-", "+", round($balance, 2)) : round($balance, 2));
                })->
                addColumn('total_qty_cutting', function ($row) {
                    $rollIdsArr = collect(DB::connection("mysql_sb")->select("select id_roll from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb WHERE a.no_req = '".$row->bppbno."' and b.id_item = '".$row->id_item."' and b.status = 'Y' GROUP BY id_roll"));

                    $rollIds = $rollIdsArr->pluck("id_roll");

                    $rolls = FormCutInputDetail::selectRaw("
                            id_roll,
                            id_item,
                            detail_item,
                            lot,
                            COALESCE(roll_buyer, roll) roll,
                            MAX(qty) qty,
                            unit,
                            ROUND(SUM(total_pemakaian_roll), 2) total_pemakaian_roll,
                            ROUND(SUM(CASE WHEN short_roll < 0 THEN short_roll ELSE 0 END), 2) total_short_roll
                        ")->
                        whereNotNull("id_roll")->
                        whereIn("id_roll", $rollIds)->
                        groupBy("id_item", "id_roll")->
                        get();

                    return $rolls ? (($row->unit == 'YARD' || $row->unit == 'YRD') ? round($rolls->sum("qty") * 1.09361 , 2) : round($rolls->sum("qty"), 2) ) : 0;
                })->
                addColumn('total_pakai_cutting', function ($row) {
                    $rollIdsArr = collect(DB::connection("mysql_sb")->select("select id_roll from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb WHERE a.no_req = '".$row->bppbno."' and b.id_item = '".$row->id_item."' and b.status = 'Y' GROUP BY id_roll"));

                    $rollIds = $rollIdsArr->pluck("id_roll");

                    $rolls = FormCutInputDetail::selectRaw("
                            id_roll,
                            id_item,
                            detail_item,
                            lot,
                            COALESCE(roll_buyer, roll) roll,
                            MAX(qty) qty,
                            unit,
                            ROUND(SUM(total_pemakaian_roll), 2) total_pemakaian_roll,
                            ROUND(SUM(CASE WHEN short_roll < 0 THEN short_roll ELSE 0 END), 2) total_short_roll
                        ")->
                        whereNotNull("id_roll")->
                        whereIn("id_roll", $rollIds)->
                        groupBy("id_item", "id_roll")->
                        get();

                    return $rolls ? (($row->unit == 'YARD' || $row->unit == 'YRD') ? round($rolls->sum("total_pemakaian_roll") * 1.0361, 2) : round($rolls->sum("total_pemakaian_roll"), 2) ) : 0;
                })->
                addColumn('total_short_cutting', function ($row) {
                    $rollIdsArr = collect(DB::connection("mysql_sb")->select("select id_roll from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb WHERE a.no_req = '".$row->bppbno."' and b.id_item = '".$row->id_item."' and b.status = 'Y' GROUP BY id_roll"));

                    $rollIds = $rollIdsArr->pluck("id_roll");

                    $rolls = FormCutInputDetail::selectRaw("
                            id_roll,
                            id_item,
                            detail_item,
                            lot,
                            COALESCE(roll_buyer, roll) roll,
                            MAX(qty) qty,
                            unit,
                            ROUND(SUM(total_pemakaian_roll), 2) total_pemakaian_roll,
                            ROUND(SUM(CASE WHEN short_roll < 0 THEN short_roll ELSE 0 END), 2) total_short_roll
                        ")->
                        whereNotNull("id_roll")->
                        whereIn("id_roll", $rollIds)->
                        groupBy("id_item", "id_roll")->
                        get();

                    return $rolls ? (($row->unit == 'YARD' || $row->unit == 'YRD') ? round($rolls->sum("total_short_roll") * 1.0361, 2) : round($rolls->sum("total_short_roll"), 2) ) : 0;
                })->
                addColumn('total_pakai_balance', function ($row) {
                    $rollIdsArr = collect(DB::connection("mysql_sb")->select("select id_roll from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb WHERE a.no_req = '".$row->bppbno."' and b.id_item = '".$row->id_item."' and b.status = 'Y' GROUP BY id_roll"));

                    $rollIds = $rollIdsArr->pluck("id_roll");

                    $rolls = FormCutInputDetail::selectRaw("
                            id_roll,
                            id_item,
                            detail_item,
                            lot,
                            COALESCE(roll_buyer, roll) roll,
                            MAX(qty) qty,
                            unit,
                            ROUND(SUM(total_pemakaian_roll), 2) total_pemakaian_roll,
                            ROUND(SUM(CASE WHEN short_roll < 0 THEN short_roll ELSE 0 END), 2) total_short_roll
                        ")->
                        whereNotNull("id_roll")->
                        whereIn("id_roll", $rollIds)->
                        groupBy("id_item", "id_roll")->
                        get();

                    $balance = $rolls ? $row->qty_out - (($row->unit == 'YARD' || $row->unit == 'YRD') ? $rolls->sum("total_pemakaian_roll") * 1.0361 : $rolls->sum("total_pemakaian_roll") ) : $row->qty_out;

                    return $balance > 0 ? round($balance, 2) : ($balance < 0 ? ( str_replace("-", "+", round($balance, 2)) ) : round($balance, 2));
                })->
                toJson();
        }

        return view('cutting.report.pemakaian-roll', ['page' => 'dashboard-cutting', "subPageGroup" => "cutting-report", "subPage" => "pemakaian-roll"]);
    }

    public function detailPemakaianRoll (Request $request)
    {
        $rollIdsArr = collect(DB::connection("mysql_sb")->select("select id_roll, id_item, item_desc, no_lot, no_roll, satuan, COALESCE(retur.tgl_dok, '-') tgl_dok, b.qty_out from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb LEFT JOIN (select * from whs_inmaterial_fabric where no_dok like '%RI%' and supplier = 'Production - Cutting') retur on a.no_bppb = retur.no_invoice WHERE a.no_req = '".$request->no_req."' and b.id_item = '".$request->id_item."' and b.status = 'Y' GROUP BY id_roll"));

        $rollData = collect();
        foreach ($rollIdsArr as $rollId) {
            $rolls = FormCutInputDetail::selectRaw("
                id_roll,
                id_item,
                detail_item,
                lot,
                COALESCE(roll_buyer, roll) roll,
                MAX(qty) qty,
                unit,
                ROUND(SUM(total_pemakaian_roll), 2) total_pemakaian_roll,
                ROUND(MAX(qty) - SUM(total_pemakaian_roll), 2) total_sisa_kain_1,
                ROUND(MIN(CASE WHEN status != 'extension' AND status != 'extension complete' THEN (sisa_kain) ELSE (qty - total_pemakaian_roll) END), 2) total_sisa_kain,
                ROUND((SUM(total_pemakaian_roll) + MIN(CASE WHEN status != 'extension' AND status != 'extension complete' THEN (sisa_kain) ELSE (qty - total_pemakaian_roll) END)) - MAX(qty), 2) total_short_roll,
                CONCAT(ROUND((((SUM(total_pemakaian_roll) + MIN(CASE WHEN status != 'extension' AND status != 'extension complete' THEN (sisa_kain) ELSE (qty - total_pemakaian_roll) END)) - MAX(qty))/(SUM(total_pemakaian_roll) + MIN(CASE WHEN status != 'extension' AND status != 'extension complete' THEN (sisa_kain) ELSE (qty - total_pemakaian_roll) END)) * 100), 2), ' %') total_short_roll_percentage,
                '".$rollId->tgl_dok."' tanggal_return
            ")->
            whereNotNull("id_roll")->
            whereIn("status", ['complete', 'need extension', 'extension complete'])->
            where("id_roll", $rollId->id_roll)->
            groupBy("id_item", "id_roll")->
            first();

            if ($rolls) {
                $rollData->push($rolls);
            } else {
                $rollData->push(collect([
                    "id_roll" => $rollId->id_roll,
                    "id_item" => $rollId->id_item,
                    "detail_item" => $rollId->item_desc,
                    "lot" => $rollId->no_lot,
                    "roll" => $rollId->no_roll,
                    "qty" => $rollId->qty_out,
                    "unit" => $rollId->satuan,
                    "total_pemakaian_roll" => 0,
                    "total_sisa_kain" => 0,
                    "total_short_roll" => 0,
                    "total_short_roll_percentage" => '0.00 %',
                    "tanggal_return" => $rollId->tgl_dok
                ]));
            }
        }

        return DataTables::of($rollData)->toJson();
    }

    public function totalPemakaianRoll(Request $request)
    {
        $dateFrom = $request->dateFrom ? $request->dateFrom : date('Y-m-d');
        $dateTo = $request->dateTo ? $request->dateTo : date('Y-m-d');

        $filterQuery = "";
        if ($request->bppbno) {
            $filterQuery = " and b.no_bppb LIKE '%".$request->bppbno."%'";
        }
        if ($request->bppbdate) {
            $filterQuery = " and b.bppbdate LIKE '%".$request->bppbdate."%'";
        }
        if ($request->no_ws) {
            $filterQuery = " and ac.kpno LIKE '%".$request->no_ws."%'";
        }
        if ($request->styleno) {
            $filterQuery = " and ac.styleno LIKE '%".$request->styleno."%'";
        }
        if ($request->buyer) {
            $filterQuery = " and ms.supplier LIKE '%".$request->buyer."%'";
        }
        if ($request->id_item) {
            $filterQuery = " and a.id_item LIKE '%".$request->id_item."%'";
        }
        if ($request->itemdesc) {
            $filterQuery = " and mi.itemdesc LIKE '%".$request->itemdesc."%'";
        }

        $requestRoll = DB::connection("mysql_sb")->select("
            select a.*,b.no_bppb no_out, COALESCE(total_roll,0) roll_out, ROUND(COALESCE(qty_out,0), 2) qty_out, c.no_dok no_retur, COALESCE(total_roll_ri,0) roll_retur, ROUND(COALESCE(qty_out_ri,0), 2) qty_retur from (select bppbno,bppbdate,s.supplier tujuan,ac.kpno no_ws,ac.styleno,ms.supplier buyer,a.id_item,
            REPLACE(mi.itemdesc, '\"', '\\\\\"') itemdesc,a.qty qty_req,a.unit, idws_act no_ws_aktual
            from bppb_req a inner join mastersupplier s on a.id_supplier=s.id_supplier
            inner join jo_det jod on a.id_jo=jod.id_jo
            inner join so on jod.id_so=so.id
            inner join act_costing ac on so.id_cost=ac.id
            inner join mastersupplier ms on ac.id_buyer=ms.id_supplier
            inner join masteritem mi on a.id_item=mi.id_item
            where bppbno like '%RQ-F%' and a.id_supplier = '432' and bppbdate between '".$dateFrom."' and '".$dateTo."' ".$filterQuery."
            group by a.id_item,a.bppbno
            order by bppbdate,bppbno desc) a left join
            (select a.no_bppb,no_req,id_item,COUNT(id_roll) total_roll, sum(qty_out) qty_out,satuan from whs_bppb_h a INNER JOIN (select bppbno,bppbdate from bppb_req where bppbno like '%RQ-F%' and id_supplier = '432' and bppbdate between '".$dateFrom."' and '".$dateTo."' GROUP BY bppbno) b on b.bppbno = a.no_req inner join whs_bppb_det c on c.no_bppb = a.no_bppb where a.status != 'Cancel' and c.status = 'Y' GROUP BY a.no_bppb,no_req,id_item) b on b.no_req = a.bppbno and b.id_item = a.id_item left join
            (select a.no_dok, no_invoice no_req,id_item,COUNT(no_barcode) total_roll_ri, sum(qty_sj) qty_out_ri,satuan from (select * from whs_inmaterial_fabric where no_dok like '%RI%' and supplier = 'Production - Cutting' ) a INNER JOIN (select bppbno,bppbdate from bppb_req where bppbno like '%RQ-F%' and id_supplier = '432' and bppbdate between '".$dateFrom."' and '".$dateTo."' GROUP BY bppbno) b on b.bppbno = a.no_invoice INNER JOIN whs_lokasi_inmaterial c on c.no_dok = a.no_dok GROUP BY a.no_dok,no_invoice,id_item) c on c.no_req = a.bppbno and c.id_item = a.id_item
        ");

        $totalQtyRequest = 0;
        $totalRollIn = 0;
        $totalQtyIn = 0;
        $totalRollCutting = 0;
        $totalQtyCutting = 0;
        $totalRollBalance = 0;
        $totalQtyBalance = 0;
        $totalRollReturn = 0;
        $totalQtyReturn = 0;

        foreach ($requestRoll as $req) {
            $rollIdsArr = collect(DB::connection("mysql_sb")->select("select id_roll from whs_bppb_h a INNER JOIN whs_bppb_det b on b.no_bppb = a.no_bppb WHERE a.no_req = '".$req->bppbno."' and b.id_item = '".$req->id_item."' and b.status = 'Y' GROUP BY id_roll"));

            $rollIds = $rollIdsArr->pluck('id_roll');

            $rolls = FormCutInputDetail::selectRaw("
                    id_roll,
                    id_item,
                    detail_item,
                    lot,
                    COALESCE(roll_buyer, roll) roll,
                    MAX(qty) qty,
                    unit,
                    ROUND(SUM(total_pemakaian_roll), 2) total_pemakaian_roll,
                    ROUND(SUM(CASE WHEN short_roll < 0 THEN short_roll ELSE 0 END), 2) total_short_roll
                ")->
                whereNotNull("id_roll")->
                whereIn("id_roll", $rollIds)->
                groupBy("id_item", "id_roll")->
                get();

            if ($rolls->count() > 0) {
                $totalQtyRequest += $req->qty_req;
                $totalRollIn += $req->roll_out;
                $totalQtyIn += $req->qty_out;
                $totalRollCutting += $rolls->count("id_roll");
                $totalQtyCutting += (($req->unit == 'YARD' || $req->unit == 'YRD') ? round($rolls->sum("total_pemakaian_roll") * 1.0361, 2) : round($rolls->sum("total_pemakaian_roll"), 2) );
                $totalRollBalance += $rolls ? $req->roll_out - $rolls->count() : $req->roll_out;
                $totalQtyBalance += $rolls ? $req->qty_out - (($req->unit == 'YARD' || $req->unit == 'YRD') ? $rolls->sum("total_pemakaian_roll") * 1.0361 : $rolls->sum("total_pemakaian_roll") ) : $req->qty_out;
                $totalRollReturn += $req->roll_retur;
                $totalQtyReturn += $req->qty_retur;
            }
        }

        return array(
            "totalQtyRequest" => $totalQtyRequest,
            "totalRollIn" => $totalRollIn,
            "totalQtyIn" => $totalQtyIn,
            "totalRollCutting" => $totalRollCutting,
            "totalQtyCutting" => $totalQtyCutting,
            "totalRollBalance" => $totalRollBalance,
            "totalQtyBalance" => $totalQtyBalance,
            "totalRollReturn" => $totalRollReturn,
            "totalQtyReturn" => $totalQtyReturn
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @param  \Illuminate\Http\Request  $request
     */

    public function export(Request $request)
    {
        ini_set("max_execution_time", 36000);

        return Excel::download(new ExportReportCuttingSinglePage($request->dateFrom, $request->dateTo), 'Report Cutting.xlsx');
    }

    public function pemakaianRollExport(Request $request)
    {
        ini_set("max_execution_time", 36000);

        return Excel::download(new ExportPemakaianKain($request->dateFrom, $request->dateTo), 'Report Detail Pemakaian Kain.xlsx');
    }

    public function detailPemakaianRollExport(Request $request)
    {
        ini_set("max_execution_time", 36000);

        return Excel::download(new ExportDetailPemakaianKain($request->no_req, $request->id_item), 'Report Detail Pemakaian Kain.xlsx');
    }

    public function cuttingDaily(Request $request) {
        if ($request->ajax()) {
            $additionalQuery = "";

            if ($request->dateFrom) {
                $additionalQuery .= " and COALESCE(DATE(waktu_selesai), DATE(waktu_mulai), tgl_form_cut) >= '".$request->dateFrom."'";
            }

            if ($request->dateTo) {
                $additionalQuery .= " and COALESCE(DATE(waktu_selesai), DATE(waktu_mulai), tgl_form_cut) <= '".$request->dateTo."'";
            }

            $keywordQuery = "";
            if ($request->search["value"]) {
                $keywordQuery = "
                    and (
                        marker_cutting.tgl_form_cut like '%" . $request->search["value"] . "%' OR
                        marker_cutting.meja like '%" . $request->search["value"] . "%' OR
                        marker_cutting.buyer like '%" . $request->search["value"] . "%' OR
                        marker_cutting.act_costing_ws like '%" . $request->search["value"] . "%' OR
                        marker_cutting.style like '%" . $request->search["value"] . "%' OR
                        marker_cutting.color like '%" . $request->search["value"] . "%' OR
                        marker_cutting.notes like '%" . $request->search["value"] . "%'
                    )
                ";
            }

            $reportCutting = DB::select("
                SELECT
                    marker_cutting.tgl_form_cut,
                    UPPER(marker_cutting.meja) meja,
                    marker_cutting.buyer,
                    marker_cutting.act_costing_ws,
                    marker_cutting.style,
                    marker_cutting.color,
                    marker_cutting.panel,
                    SUM((marker_cutting.form_gelar * marker_cutting.ratio) + COALESCE(marker_cutting.diff, 0)) qty
                FROM
                    (
                        SELECT
                            marker_input.kode,
                            GROUP_CONCAT(form_cut.no_form, form_cut.meja) no_form_meja,
                            form_cut.id_meja,
                            form_cut.meja,
                            form_cut.tgl_form_cut,
                            marker_input.buyer,
                            marker_input.act_costing_id,
                            marker_input.act_costing_ws,
                            marker_input.style,
                            marker_input.color,
                            marker_input.panel,
                            marker_input.cons_ws,
                            marker_input.unit_panjang_marker unit,
                            marker_input_detail.so_det_id,
                            CONCAT(master_sb_ws.size, CASE WHEN master_sb_ws.dest != '-' AND master_sb_ws.dest IS NOT NULL THEN CONCAT(' - ', master_sb_ws.dest) ELSE '' END) size,
                            marker_input_detail.ratio,
                            COALESCE(marker_input.notes, form_cut.notes) notes,
                            marker_input.gelar_qty marker_gelar,
                            SUM(form_cut.qty_ply) spreading_gelar,
                            SUM(COALESCE(form_cut.total_lembar, form_cut.detail)) form_gelar,
                            SUM(modify_size_qty.difference_qty) diff
                        FROM
                        marker_input
                        INNER JOIN
                            marker_input_detail on marker_input_detail.marker_id = marker_input.id
                        INNER JOIN
                            master_sb_ws on master_sb_ws.id_so_det = marker_input_detail.so_det_id
                        INNER JOIN
                            (
                                SELECT
                                    meja.id id_meja,
                                    meja.`name` meja,
                                    COALESCE(DATE(waktu_selesai), DATE(waktu_mulai), tgl_form_cut) tgl_form_cut,
                                    form_cut_input.id_marker,
                                    form_cut_input.no_form,
                                    form_cut_input.qty_ply,
                                    form_cut_input.total_lembar,
                                    form_cut_input.notes,
                                    SUM(form_cut_input_detail.lembar_gelaran) detail
                                FROM
                                    form_cut_input
                                    LEFT JOIN users meja ON meja.id = form_cut_input.no_meja
                                    INNER JOIN form_cut_input_detail ON form_cut_input_detail.no_form_cut_input = form_cut_input.no_form
                                WHERE
                                    form_cut_input.`status` = 'SELESAI PENGERJAAN'
                                    AND form_cut_input.waktu_mulai is not null
                                    AND form_cut_input.tgl_form_cut >= DATE(NOW()-INTERVAL 6 MONTH)
                                    AND form_cut_input_detail.updated_at >= DATE(NOW()-INTERVAL 6 MONTH)
                                    ".$additionalQuery."
                                GROUP BY
                                    form_cut_input.no_form
                            ) form_cut on form_cut.id_marker = marker_input.kode
                        LEFT JOIN
                            modify_size_qty ON modify_size_qty.no_form = form_cut.no_form AND modify_size_qty.so_det_id = marker_input_detail.so_det_id
                        where
                            (marker_input.cancel IS NULL OR marker_input.cancel != 'Y')
                            AND marker_input_detail.ratio > 0
                        group by
                            marker_input.id,
                            marker_input_detail.so_det_id,
                            form_cut.tgl_form_cut,
                            form_cut.meja
                    ) marker_cutting
                GROUP BY
                    marker_cutting.id_meja,
                    marker_cutting.act_costing_id,
                    marker_cutting.color,
                    marker_cutting.panel,
                    marker_cutting.tgl_form_cut
                ORDER BY
                    marker_cutting.id_meja,
                    marker_cutting.tgl_form_cut,
                    marker_cutting.panel,
                    marker_cutting.act_costing_id,
                    marker_cutting.color
            ");

            return DataTables::of($reportCutting)->toJson();
        }

        return view('cutting.report.report-cutting-output-daily', ['page' => 'dashboard-cutting', "subPageGroup" => "cutting-report", "subPage" => "cutting-daily"]);
    }

    public function totalCuttingDaily(Request $request) {
        $additionalQuery = "";

        if ($request->dateFrom) {
            $additionalQuery .= " and COALESCE(DATE(waktu_selesai), DATE(waktu_mulai), tgl_form_cut) >= '".$request->dateFrom."'";
        }

        if ($request->dateTo) {
            $additionalQuery .= " and COALESCE(DATE(waktu_selesai), DATE(waktu_mulai), tgl_form_cut) <= '".$request->dateTo."'";
        }

        $tanggalFilter = "";
        if ($request->tanggal) {
            $tanggalFilter = " and form_cut.tgl_form_cut LIKE '%".$request->tanggal."%'";
        }
        $noMejaFilter = "";
        if ($request->noMeja) {
            $noMejaFilter = " and form_cut.meja LIKE '%".$request->noMeja."%'";
        }
        $buyerFilter = "";
        if ($request->buyer) {
            $buyerFilter = " and marker_input.buyer LIKE '%".$request->buyer."%'";
        }
        $wsFilter = "";
        if ($request->ws) {
            $wsFilter = " and marker_input.act_costing_ws LIKE '%".$request->ws."%'";
        }
        $styleFilter = "";
        if ($request->style) {
            $styleFilter = " and marker_input.style LIKE '%".$request->style."%'";
        }
        $colorFilter = "";
        if ($request->color) {
            $colorFilter = " and marker_input.color LIKE '%".$request->color."%'";
        }
        $panelFilter = "";
        if ($request->panel) {
            $panelFilter = " and marker_input.panel LIKE '%".$request->panel."%'";
        }

        $reportCutting = collect(
            DB::select("
                SELECT
                    marker_cutting.tgl_form_cut,
                    UPPER(marker_cutting.meja) meja,
                    marker_cutting.buyer,
                    marker_cutting.act_costing_ws,
                    marker_cutting.style,
                    marker_cutting.color,
                    marker_cutting.panel,
                    SUM((marker_cutting.form_gelar * marker_cutting.ratio) + COALESCE(marker_cutting.diff, 0)) qty
                FROM
                    (
                        SELECT
                            marker_input.kode,
                            GROUP_CONCAT(form_cut.no_form, form_cut.meja) no_form_meja,
                            form_cut.id_meja,
                            form_cut.meja,
                            form_cut.tgl_form_cut,
                            marker_input.buyer,
                            marker_input.act_costing_id,
                            marker_input.act_costing_ws,
                            marker_input.style,
                            marker_input.color,
                            marker_input.panel,
                            marker_input.cons_ws,
                            marker_input.unit_panjang_marker unit,
                            marker_input_detail.so_det_id,
                            CONCAT(master_sb_ws.size, CASE WHEN master_sb_ws.dest != '-' AND master_sb_ws.dest IS NOT NULL THEN CONCAT(' - ', master_sb_ws.dest) ELSE '' END) size,
                            marker_input_detail.ratio,
                            COALESCE(marker_input.notes, form_cut.notes) notes,
                            marker_input.gelar_qty marker_gelar,
                            SUM(form_cut.qty_ply) spreading_gelar,
                            SUM(COALESCE(form_cut.total_lembar, form_cut.detail)) form_gelar,
                            SUM(modify_size_qty.difference_qty) diff
                        FROM
                        marker_input
                        INNER JOIN
                            marker_input_detail on marker_input_detail.marker_id = marker_input.id
                        INNER JOIN
                            master_sb_ws on master_sb_ws.id_so_det = marker_input_detail.so_det_id
                        INNER JOIN
                            (
                                SELECT
                                    meja.id id_meja,
                                    meja.`name` meja,
                                    COALESCE(DATE(waktu_selesai), DATE(waktu_mulai), tgl_form_cut) tgl_form_cut,
                                    form_cut_input.id_marker,
                                    form_cut_input.no_form,
                                    form_cut_input.qty_ply,
                                    form_cut_input.total_lembar,
                                    form_cut_input.notes,
                                    SUM(form_cut_input_detail.lembar_gelaran) detail
                                FROM
                                    form_cut_input
                                    LEFT JOIN users meja ON meja.id = form_cut_input.no_meja
                                    INNER JOIN form_cut_input_detail ON form_cut_input_detail.no_form_cut_input = form_cut_input.no_form
                                WHERE
                                    form_cut_input.`status` = 'SELESAI PENGERJAAN'
                                    AND form_cut_input.waktu_mulai is not null
                                    AND form_cut_input.tgl_form_cut >= DATE(NOW()-INTERVAL 6 MONTH)
                                    AND form_cut_input_detail.updated_at >= DATE(NOW()-INTERVAL 6 MONTH)
                                    ".$additionalQuery."
                                GROUP BY
                                    form_cut_input.no_form
                            ) form_cut on form_cut.id_marker = marker_input.kode
                        LEFT JOIN
                            modify_size_qty ON modify_size_qty.no_form = form_cut.no_form AND modify_size_qty.so_det_id = marker_input_detail.so_det_id
                        where
                            (marker_input.cancel IS NULL OR marker_input.cancel != 'Y')
                            AND marker_input_detail.ratio > 0
                            ".$tanggalFilter."
                            ".$noMejaFilter."
                            ".$buyerFilter."
                            ".$wsFilter."
                            ".$styleFilter."
                            ".$colorFilter."
                            ".$panelFilter."
                        group by
                            marker_input.id,
                            marker_input_detail.so_det_id,
                            form_cut.tgl_form_cut,
                            form_cut.meja
                    ) marker_cutting
                GROUP BY
                    marker_cutting.id_meja,
                    marker_cutting.act_costing_id,
                    marker_cutting.color,
                    marker_cutting.panel,
                    marker_cutting.tgl_form_cut
                ORDER BY
                    marker_cutting.id_meja,
                    marker_cutting.tgl_form_cut,
                    marker_cutting.panel,
                    marker_cutting.act_costing_id,
                    marker_cutting.color
            ")
        );

        return array(
            "totalCuttingDaily" => $reportCutting->sum("qty")
        );
    }

    public function cuttingDailyExport(Request $request)
    {
        ini_set("max_execution_time", 36000);

        return Excel::download(new ExportReportCuttingDaily($request->dateFrom, $request->dateTo), 'Report Cutting Output Daily.xlsx');
    }

    public function trackCuttingOutput(Request $request) {
        if ($request->ajax()) {
            if ($request->type == "supplier") {
                $suppliersQuery = DB::connection('mysql_sb')->table('mastersupplier')->
                    selectRaw('Id_Supplier as id, Supplier as name')->
                    leftJoin('act_costing', 'act_costing.id_buyer', '=', 'mastersupplier.Id_Supplier')->
                    where('mastersupplier.tipe_sup', 'C')->
                    where('status', '!=', 'CANCEL')->
                    where('type_ws', 'STD')->
                    where('cost_date', '>=', '2023-01-01');
                $suppliers = $suppliersQuery->
                    orderBy('Supplier', 'ASC')->
                    groupBy('Id_Supplier', 'Supplier')->
                    get();

                return $suppliers;
            }

            if ($request->type == "order") {
                $orderSql = DB::connection('mysql_sb')->
                    table('act_costing')->
                    selectRaw('
                        id as id_ws,
                        kpno as no_ws
                    ')->
                    where('status', '!=', 'CANCEL')->
                    where('type_ws', 'STD')->
                    where('cost_date', '>=', '2023-01-01');
                if ($request->supplier) {
                    $orderSql->where('id_buyer', $request->supplier);
                }
                $orders = $orderSql->
                    orderBy('cost_date', 'desc')->
                    orderBy('kpno', 'asc')->
                    groupBy('kpno')->
                    get();

                return $orders;
            }
        }

        return view('cutting.report.track-cutting-output', ["subPageGroup" => "cutting-report", "subPage" => "cutting-track", "page" => "dashboard-cutting"]);
    }

    public function cuttingOrderOutputExport (Request $request) {
        ini_set("max_execution_time", 36000);

        $dateFrom = $request->dateFrom;
        $dateTo = $request->dateTo;
        $groupBy = $request->groupBy;
        $order = $request->order;
        $buyer = $request->buyer;

        return Excel::download(new CuttingOrderOutputExport($dateFrom, $dateTo, $groupBy, $order, $buyer), 'order_output.xlsx');
    }
}

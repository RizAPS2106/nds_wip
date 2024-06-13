<?php

namespace App\Http\Controllers\Cutting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Marker;
use App\Models\MarkerDetail;
use App\Models\FormCutInput;
use App\Models\FormCutInputDetail;
use App\Models\FormCutInputLostTime;
use App\Models\ScannedItem;
use App\Models\Part;
use App\Models\PartForm;
use App\Models\User;
use App\Models\ModifySizeQty;
use App\Models\Stocker;
use App\Models\StockerDetail;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use DB;

class CompletedFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    public function cutting(Request $request) {
        $additionalQuery = "";

        if ($request->ajax()) {
            if ($request->dateFrom) {
                $additionalQuery .= " and DATE(a.waktu_selesai) >= '" . $request->dateFrom . "' ";
            }

            if ($request->dateTo) {
                $additionalQuery .= " and DATE(a.waktu_selesai) <= '" . $request->dateTo . "' ";
            }

            $keywordQuery = "";
            if ($request->search["value"]) {
                $keywordQuery = "
                    and (
                        a.id_marker like '%" . $request->search["value"] . "%' OR
                        a.no_meja like '%" . $request->search["value"] . "%' OR
                        a.no_form like '%" . $request->search["value"] . "%' OR
                        a.tgl_form_cut like '%" . $request->search["value"] . "%' OR
                        b.act_costing_ws like '%" . $request->search["value"] . "%' OR
                        panel like '%" . $request->search["value"] . "%' OR
                        b.color like '%" . $request->search["value"] . "%' OR
                        a.status like '%" . $request->search["value"] . "%' OR
                        users.name like '%" . $request->search["value"] . "%'
                    )
                ";
            }

            $data_spreading = DB::select("
                SELECT
                    a.id,
                    a.no_meja,
                    a.id_marker,
                    a.no_form,
                    a.no_cut,
                    a.tgl_form_cut,
                    b.id marker_id,
                    b.act_costing_ws ws,
                    b.style,
                    CONCAT(b.panel, ' - ', b.urutan_marker) panel,
                    b.color,
                    a.status,
                    UPPER(users.name) nama_meja,
                    b.panjang_marker,
                    UPPER(b.unit_panjang_marker) unit_panjang_marker,
                    b.comma_marker,
                    UPPER(b.unit_comma_marker) unit_comma_marker,
                    b.lebar_marker,
                    UPPER(b.unit_lebar_marker) unit_lebar_marker,
                    CONCAT(COALESCE(a.total_lembar, '0'), '/', a.qty_ply) ply_progress,
                    COALESCE(a.qty_ply, 0) qty_ply,
                    COALESCE(b.gelar_qty, 0) gelar_qty,
                    COALESCE(a.total_lembar, '0') total_lembar,
                    b.po_marker,
                    b.urutan_marker,
                    b.cons_marker,
                    UPPER(b.tipe_marker) tipe_marker,
                    a.tipe_form_cut,
                    COALESCE(b.notes, '-') notes,
                    GROUP_CONCAT(DISTINCT CONCAT(COALESCE(master_size_new.size, marker_input_detail.size), '(', marker_input_detail.ratio, ')') ORDER BY COALESCE(master_size_new.urutan, marker_input_detail.id) ASC SEPARATOR ', ') marker_details,
                    cutting_plan.tgl_plan,
                    cutting_plan.app
                FROM `form_cut_input` a
                left join cutting_plan on cutting_plan.no_form_cut_input = a.no_form
                left join users on users.id = a.no_meja
                left join marker_input b on a.id_marker = b.kode and b.cancel = 'N'
                left join marker_input_detail on b.id = marker_input_detail.marker_id
                left join master_size_new on marker_input_detail.size = master_size_new.size
                where
                    a.id is not null and
                    a.status = 'SELESAI PENGERJAAN'
                    " . $additionalQuery . "
                    " . $keywordQuery . "
                GROUP BY a.id
                ORDER BY
                    FIELD(a.tipe_form_cut, null, 'PILOT', 'NORMAL', 'MANUAL'),
                    FIELD(a.app, 'Y', 'N', null),
                    a.no_form desc,
                    a.updated_at desc
            ");

            return DataTables::of($data_spreading)->toJson();
        }

        $meja = User::select("id", "name", "username")->where('type', 'meja')->get();

        return view('cutting.completed-form.completed-form', ['meja' => $meja, 'page' => 'dashboard-cutting', "subPage" => "manage-cutting"]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function detailCutting($id) {
        $formCutInputData = FormCutInput::leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.id_marker")->leftJoin("users", "users.id", "=", "form_cut_input.no_meja")->where('form_cut_input.id', $id)->first();

        $actCostingData = DB::connection("mysql_sb")->table('act_costing')->selectRaw('act_costing.id id, act_costing.styleno style, mastersupplier.Supplier buyer')->leftJoin('mastersupplier', 'mastersupplier.Id_Supplier', 'act_costing.id_buyer')->groupBy('act_costing.id')->where('act_costing.id', $formCutInputData->act_costing_id)->get();

        $markerDetailData = MarkerDetail::selectRaw("
                marker_input.kode kode_marker,
                marker_input_detail.size,
                marker_input_detail.so_det_id,
                marker_input_detail.ratio,
                marker_input_detail.cut_qty
            ")->
            leftJoin("marker_input", "marker_input.id", "=", "marker_input_detail.marker_id")->
            where("marker_input.kode", $formCutInputData->kode)->
            where("marker_input.cancel", "N")->
            get();

        $lostTimeData = FormCutInputLostTime::where('form_cut_input_id', $id)->get();

        $meja = User::select("id", "name", "username")->where('type', 'meja')->get();

        return view("cutting.completed-form.completed-form-detail", [
            'id' => $id,
            'meja' => $meja,
            'formCutInputData' => $formCutInputData,
            'actCostingData' => $actCostingData,
            'markerDetailData' => $markerDetailData,
            'lostTimeData' => $lostTimeData,
            'page' => 'dashboard-cutting',
            "subPage" => "manage-cutting"
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function updateCutting(Request $request) {
        $validatedRequest = $request->validate([
            "current_id" => "required",
            "current_id_roll" => "nullable",
            "no_form_cut_input" => "required",
            "no_meja" => "required",
            "current_id_item" => "required",
            "current_group" => "required",
            "current_group_stocker" => "nullable",
            "current_roll" => "nullable",
            "current_qty" => "required",
            "current_qty_real" => "required",
            "current_unit" => "required",
            "current_sisa_gelaran" => "required",
            "current_est_amparan" => "required",
            "current_lembar_gelaran" => "required",
            "current_kepala_kain" => "required",
            "current_sisa_tidak_bisa" => "required",
            "current_reject" => "required",
            "current_sisa_kain" => "required",
            "current_total_pemakaian_roll" => "required",
            "current_short_roll" => "required",
            "current_piping" => "required",
            "current_remark" => "required",
            "current_sambungan" => "required",
            "p_act" => "required"
        ]);

        $itemQty = ($validatedRequest["current_unit"] != "KGM" ? floatval($validatedRequest['current_qty']) : floatval($validatedRequest['current_qty_real']));
        $itemUnit = ($validatedRequest["current_unit"] != "KGM" ? "METER" : $validatedRequest['current_unit']);

        $updateTimeRecordSummary = FormCutInputDetail::selectRaw("form_cut_input_detail.*")->
            leftJoin('form_cut_input', 'form_cut_input.no_form', '=', 'form_cut_input_detail.no_form_cut_input')->
            where('form_cut_input.no_form', $validatedRequest['no_form_cut_input'])->
            where('form_cut_input_detail.id', $validatedRequest['current_id'])->
            update([
                "id_roll" => $validatedRequest['current_id_roll'],
                "id_item" => $validatedRequest['current_id_item'],
                "group_roll" => $validatedRequest['current_group'],
                "lot" => $request["current_lot"],
                "roll" => $validatedRequest['current_roll'],
                "qty" => $itemQty,
                "unit" => $itemUnit,
                "sisa_gelaran" => $validatedRequest['current_sisa_gelaran'],
                "sambungan" => $validatedRequest['current_sambungan'],
                "est_amparan" => $validatedRequest['current_est_amparan'],
                "lembar_gelaran" => $validatedRequest['current_lembar_gelaran'],
                "kepala_kain" => $validatedRequest['current_kepala_kain'],
                "sisa_tidak_bisa" => $validatedRequest['current_sisa_tidak_bisa'],
                "reject" => $validatedRequest['current_reject'],
                "sisa_kain" => $validatedRequest['current_sisa_kain'],
                "total_pemakaian_roll" => $validatedRequest['current_total_pemakaian_roll'],
                "short_roll" => $validatedRequest['current_short_roll'],
                "piping" => $validatedRequest['current_piping'],
                "remark" => $validatedRequest['current_remark'],
            ]);

        if ($updateTimeRecordSummary) {
            $itemRemain = $validatedRequest['current_sisa_kain'];

            ScannedItem::where("id_roll", $validatedRequest['current_id_roll'])->update([
                "id_item" => $validatedRequest['current_id_item'],
                "lot" => $request['current_lot'],
                "roll" => $validatedRequest['current_roll'],
                "qty" => $itemRemain,
                "unit" => $itemUnit,
            ]);

            $formCutDetails = FormCutInputDetail::where("no_form_cut_input", $validatedRequest['no_form_cut_input'])->orderBy("id", "asc")->get();
            $currentGroup = "";
            $groupNumber = 0;
            foreach ($formCutDetails as $formCutDetail) {
                if ($currentGroup != $formCutDetail->group_roll) {
                    $currentGroup = $formCutDetail->group_roll;
                    $groupNumber += 1;
                }

                $formCutDetail->group_stocker = $groupNumber;
                $formCutDetail->save();
            }

            $updateFormCut = FormCutInput::where('no_form', $validatedRequest['no_form_cut_input'])->update([
                "no_meja" => $validatedRequest['no_meja']
            ]);

            return array(
                "status" => 200,
                "message" => "alright",
            );
        }
    }

    public function updateFinish(Request $request, $id) {
        $formCutInputData = FormCutInput::selectRaw("form_cut_input.*, marker_input.color")->
            leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.id_marker")->
            where("form_cut_input.id", $id)->
            first();

        $updateFormCutInput = FormCutInput::where("id", $id)->update([
            "cons_act" => $request->consAct,
            "unit_cons_act" => $request->unitConsAct,
            "cons_act_nosr" => $request->consActNoSr,
            "unit_cons_act_nosr" => $request->unitConsActNoSr,
            "cons_ws_uprate" => $request->consWsUprate,
            "cons_marker_uprate" => $request->consMarkerUprate,
            "cons_ws_uprate_nosr" => $request->consWsUprateNoSr,
            "cons_marker_uprate_nosr" => $request->consMarkerUprateNoSr,
            "total_lembar" => $request->totalLembar,
            "operator" => $request->operator,
        ]);

        // store to part form
        $partData = Part::select('part.id')->
            where("act_costing_id", $formCutInputData->marker->act_costing_id)->
            where("act_costing_ws", $formCutInputData->marker->act_costing_ws)->
            where("panel", $formCutInputData->marker->panel)->
            where("buyer", $formCutInputData->marker->buyer)->
            where("style", $formCutInputData->marker->style)->
            first();

        if ($updateFormCutInput && $partData) {
            $checkPartForm = PartForm::where("form_id", $formCutInputData->id)->first();

            if (!$checkPartForm) {
                $lastPartForm = PartForm::select("kode")->orderBy("kode", "desc")->first();
                $urutanPartForm = $lastPartForm ? intval(substr($lastPartForm->kode, -5)) + 1 : 1;
                $kodePartForm = "PFM" . sprintf('%05s', $urutanPartForm);

                $addToPartForm = PartForm::create([
                    "kode" => $kodePartForm,
                    "part_id" => $partData->id,
                    "form_id" => $formCutInputData->id,
                    "created_at" => Carbon::now(),
                    "updated_at" => Carbon::now(),
                ]);
            }
            else {
                ini_set('max_execution_time', 360000);

                $formCutInputs = FormCutInput::selectRaw("
                        marker_input.color,
                        form_cut_input.id as id_form,
                        form_cut_input.no_cut,
                        form_cut_input.no_form as no_form
                    ")->
                    leftJoin("part_form", "part_form.form_id", "=", "form_cut_input.id")->
                    leftJoin("part", "part.id", "=", "part_form.part_id")->
                    leftJoin("part_detail", "part_detail.part_id", "=", "part.id")->
                    leftJoin("master_part", "master_part.id", "=", "part_detail.master_part_id")->
                    leftJoin("marker_input", "marker_input.kode", "=", "form_cut_input.id_marker")->
                    leftJoin("marker_input_detail", "marker_input_detail.marker_id", "=", "marker_input.id")->
                    leftJoin("master_size_new", "master_size_new.size", "=", "marker_input_detail.size")->
                    leftJoin("users", "users.id", "=", "form_cut_input.no_meja")->
                    whereRaw("part_form.id is not null")->
                    where("part.id", $partData->id)->
                    where("marker_input.color", $formCutInputData->color)->
                    where("form_cut_input.no_cut", ">=", $formCutInputData->no_cut)->
                    groupBy("form_cut_input.id")->
                    orderBy("marker_input.color", "asc")->
                    orderBy("form_cut_input.waktu_selesai", "asc")->
                    orderBy("form_cut_input.no_cut", "asc")->
                    get();

                $rangeAwal = 0;
                $sizeRangeAkhir = collect();

                $currentColor = "";
                $currentNumber = 0;

                // Loop over all forms
                foreach ($formCutInputs as $formCut) {
                    $modifySizeQty = ModifySizeQty::where("no_form", $formCut->no_form)->get();

                    // Reset cumulative data on color switch
                    if ($formCut->color != $currentColor) {
                        $rangeAwal = 0;
                        $sizeRangeAkhir = collect();

                        $currentColor = $formCut->color;
                        $currentNumber = 0;
                    }

                    // Adjust form data
                    $currentNumber++;
                    FormCutInput::where("id", $formCut->id_form)->update([
                        "no_cut" => $currentNumber
                    ]);

                    // Adjust form cut detail data
                    $formCutInputDetails = FormCutInputDetail::where("no_form_cut_input", $formCut->no_form)->orderBy("id", "asc")->get();

                    $currentGroup = "";
                    $currentGroupNumber = 0;
                    foreach ($formCutInputDetails as $formCutInputDetail) {
                        if ($currentGroup != $formCutInputDetail->group_roll) {
                            $currentGroup = $formCutInputDetail->group_roll;
                            $currentGroupNumber += 1;
                        }

                        $formCutInputDetail->group_stocker = $currentGroupNumber;
                        $formCutInputDetail->save();
                    }

                    // Adjust stocker data
                    $stockerForm = Stocker::where("form_cut_id", $formCut->id_form)->orderBy("group_stocker", "desc")->orderBy("size", "asc")->orderBy("ratio", "asc")->orderBy("part_detail_id", "asc")->get();

                    $currentStockerPart = $stockerForm->first() ? $stockerForm->first()->part_detail_id : "";
                    $currentStockerSize = "";
                    $currentStockerGroup = "initial";
                    $currentStockerRatio = 0;

                    foreach ($stockerForm as $key => $stocker) {
                        $lembarGelaran = 1;
                        if ($stocker->group_stocker) {
                            $lembarGelaran = FormCutInputDetail::where("no_form_cut_input", $formCut->no_form)->where('group_stocker', $stocker->group_stocker)->sum('lembar_gelaran');
                        } else {
                            $lembarGelaran = FormCutInputDetail::where("no_form_cut_input", $formCut->no_form)->where('group_roll', $stocker->shade)->sum('lembar_gelaran');
                        }

                        if ($currentStockerPart == $stocker->part_detail_id) {
                            if ($stockerForm->min("group_stocker") == $stocker->group_stocker && $stockerForm->filter(function ($item) use ($stocker) { return $item->size == $stocker->size; })->max("ratio") == $stocker->ratio) {
                                $modifyThis = $modifySizeQty->where("so_det_id", $stocker->so_det_id)->first();

                                if ($modifyThis) {
                                    $lembarGelaran = ($stocker->qty_ply < 1 ? 0 : $lembarGelaran) + $modifyThis->difference_qty;
                                }
                            }

                            if (isset($sizeRangeAkhir[$stocker->size]) && ($currentStockerSize != $stocker->size || $currentStockerGroup != $stocker->group_stocker || $currentStockerRatio != $stocker->ratio)) {
                                $rangeAwal = $sizeRangeAkhir[$stocker->size] + 1;
                                $sizeRangeAkhir[$stocker->size] = ($sizeRangeAkhir[$stocker->size] + $lembarGelaran);

                                $currentStockerSize = $stocker->size;
                                $currentStockerGroup = $stocker->group_stocker;
                                $currentStockerRatio = $stocker->ratio;
                            } else if (!isset($sizeRangeAkhir[$stocker->size])) {
                                $rangeAwal =  1;
                                $sizeRangeAkhir->put($stocker->size, $lembarGelaran);
                            }
                        }

                        $stocker->size && (($sizeRangeAkhir[$stocker->size] - ($rangeAwal-1)) != $stocker->qty || $stocker->qty_ply < 1) ? ($stocker->qty_ply_mod = ($sizeRangeAkhir[$stocker->size] - ($rangeAwal-1))) : $stocker->qty_ply_mod = 0;
                        $stocker->range_awal = $rangeAwal;
                        $stocker->range_akhir = $stocker->size ? $sizeRangeAkhir[$stocker->size] : 0;
                        $stocker->save();

                        if ($stocker->qty_ply < 1 && $stocker->qty_ply_mod < 1) {
                            $stocker->delete();
                        }
                    }

                    // Adjust numbering data
                    $numbers = StockerDetail::selectRaw("
                            form_cut_id,
                            act_costing_ws,
                            color,
                            panel,
                            so_det_id,
                            size,
                            no_cut_size,
                            MAX(number) number
                        ")->
                        where("form_cut_id", $formCut->id_form)->
                        whereRaw("(cancel is null OR cancel = 'N')")->
                        groupBy("form_cut_id", "size")->
                        get();

                    foreach ($numbers as $number) {
                        if (isset($sizeRangeAkhir[$number->size])) {
                            if ($number->number > $sizeRangeAkhir[$number->size]) {
                                StockerDetail::where("form_cut_id", $number->form_cut_id)->
                                    where("size", $number->size)->
                                    where("number", ">", $sizeRangeAkhir[$number->size])->
                                    update([
                                        "cancel" => "Y"
                                    ]);
                            } else {
                                StockerDetail::where("form_cut_id", $number->form_cut_id)->
                                    where("size", $number->size)->
                                    where("number", "<=", $sizeRangeAkhir[$number->size])->
                                    where("cancel", "Y")->
                                    update([
                                        "cancel" => "N"
                                    ]);
                            }

                            if ($number->number < $sizeRangeAkhir[$number->size]) {
                                $stockerDetailCount = StockerDetail::select("kode")->orderBy("id", "desc")->first() ? str_replace("WIP-", "", StockerDetail::select("kode")->orderBy("id", "desc")->first()->kode) + 1 : 1;
                                $noCutSize = substr($number->no_cut_size, 0, strlen($number->size)+2);

                                $no = 0;
                                for ($i = $number->number; $i < $sizeRangeAkhir[$number->size]; $i++) {
                                    StockerDetail::create([
                                        "kode" => "WIP-".($stockerDetailCount+$no),
                                        "form_cut_id" => $number->form_cut_id,
                                        "act_costing_ws" => $number->act_costing_ws,
                                        "color" => $number->color,
                                        "panel" => $number->panel,
                                        "so_det_id" => $number->so_det_id,
                                        "size" => $number->size,
                                        "no_cut_size" => $noCutSize. sprintf('%04s', ($i+1)),
                                        "number" => $i+1
                                    ]);

                                    $no++;
                                }
                            }
                        }
                    }
                }
            }

            return array(
                "status" => 200,
                "message" => "alright",
            );
        }

        return array(
            "status" => 400,
            "message" => "nothing really matter anymore",
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function destroySpreadingRoll($id) {
        $formCutDetail = FormCutInputDetail::find($id);

        if ($formCutDetail) {
            if ($formCutDetail->id_roll) {
                $formCutDetailRoll = ScannedItem::where("id_roll", $formCutDetail->id_roll)->first();

                if ($formCutDetailRoll) {
                    $formCutDetailRoll->qty += ($formCutDetail->qty - $formCutDetail->sisa_kain);
                    $formCutDetailRoll->save();
                }
            }

            DB::table("form_cut_input_detail_delete")->insert([
                "no_form_cut_input" => $formCutDetail['no_form_cut_input'],
                "id_roll" => $formCutDetail['id_roll'],
                "id_item" => $formCutDetail['id_item'],
                "color_act" => $formCutDetail['color_act'],
                "detail_item" => $formCutDetail['detail_item'],
                "group_roll" => $formCutDetail['group_roll'],
                "lot" => $formCutDetail['lot'],
                "roll" => $formCutDetail['roll'],
                "qty" => $formCutDetail['qty'],
                "unit" => $formCutDetail['unit'],
                "sisa_gelaran" => $formCutDetail['sisa_gelaran'],
                "sambungan" => $formCutDetail['sambungan'],
                "est_amparan" => $formCutDetail['est_amparan'],
                "lembar_gelaran" => $formCutDetail['lembar_gelaran'],
                "average_time" => $formCutDetail['average_time'],
                "kepala_kain" => $formCutDetail['kepala_kain'],
                "sisa_tidak_bisa" => $formCutDetail['sisa_tidak_bisa'],
                "reject" => $formCutDetail['reject'],
                "sisa_kain" => ($formCutDetail['sisa_kain'] ? $formCutDetail['sisa_kain'] : 0),
                "total_pemakaian_roll" => $formCutDetail['total_pemakaian_roll'],
                "short_roll" => $formCutDetail['short_roll'],
                "piping" => $formCutDetail['piping'],
                "remark" => $formCutDetail['remark'],
                "status" => $formCutDetail['status'],
                "metode" => $formCutDetail['metode'],
                "group_stocker" => $formCutDetail['group_stocker'],
                "created_at" => $formCutDetail['created_at'],
                "updated_at" => $formCutDetail['updated_at'],
                "deleted_by" => Auth::user()->username,
                "deleted_at" => Carbon::now(),
            ]);

            if ($formCutDetail->delete()) {
                return array(
                    "status" => 200,
                    "message" => "alright"
                );
            }
        }

        return array(
            "status" => 400,
            "message" => "nothing really matter anymore"
        );
    }
}

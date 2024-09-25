<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\SignalBit\MasterPlan;
use App\Models\SignalBit\ActCosting;
use DB;

class TrackOrderOutput extends Component
{
    public $suppliers;
    public $orders;
    public $selectedSupplier;
    public $selectedOrder;

    public $orderFilter;
    public $dailyOrderGroup;
    public $dailyOrderOutputs;
    public $loadingOrder;

    public $dateFromFilter;
    public $dateToFilter;

    public $colorFilter;
    public $lineFilter;
    public $sizeFilter;

    public $outputType;

    public $groupBy;

    public $baseUrl;

    public function mount()
    {
        $this->suppliers = null;
        $this->orders = null;
        $this->selectedSupplier = null;
        $this->selectedOrder = null;
        $this->dailyOrderGroup = null;
        $this->dailyOrderOutputs = null;
        $this->loadingOrderOutput = false;

        $this->dateFromFilter = date('Y-m-d', strtotime('-7 days'));
        $this->dateToFilter = date("Y-m-d");

        $this->outputType = null;

        $this->colorFilter = null;
        $this->lineFilter = null;
        $this->sizeFilter = null;

        $this->groupBy = "size";
        $this->baseUrl = url('/');
    }

    public function clearFilter()
    {
        $this->colorFilter = null;
        $this->lineFilter = null;
        $this->sizeFilter = null;
    }

    public function render()
    {
        $this->loadingOrderOutput = false;

        $this->suppliers = DB::connection('mysql_sb')->table('mastersupplier')->
            selectRaw('Id_Supplier as id, Supplier as name')->
            leftJoin('act_costing', 'act_costing.id_buyer', '=', 'mastersupplier.Id_Supplier')->
            where('mastersupplier.tipe_sup', 'C')->
            where('status', '!=', 'CANCEL')->
            where('type_ws', 'STD')->
            where('cost_date', '>=', '2023-01-01')->
            orderBy('Supplier', 'ASC')->
            groupBy('Id_Supplier', 'Supplier')->
            get();

        $orderSql = DB::connection('mysql_sb')->
            table('act_costing')->
            selectRaw('
                id as id_ws,
                kpno as no_ws
            ')->
            where('status', '!=', 'CANCEL')->
            where('cost_date', '>=', '2023-01-01')->
            where('type_ws', 'STD');
        if ($this->selectedSupplier) {
            $orderSql->where('id_buyer', $this->selectedSupplier);
        }
        $this->orders = $orderSql->
            orderBy('cost_date', 'desc')->
            orderBy('kpno', 'asc')->
            groupBy('kpno')->
            get();

        if ($this->selectedOrder) {
            $orderFilterSql = MasterPlan::selectRaw("
                    master_plan.tgl_plan tanggal,
                    act_costing.kpno ws,
                    act_costing.styleno style,
                    master_plan.color,
                    COALESCE(rfts.sewing_line, master_plan.sewing_line) as sewing_line
                    ".($this->groupBy == "size" ? ", so_det.id as so_det_id, so_det.size, (CASE WHEN so_det.dest is not null AND so_det.dest != '-' THEN CONCAT(so_det.size, ' - ', so_det.dest) ELSE so_det.size END) sizedest" : "")."
                ")->
                leftJoin("act_costing", "act_costing.id", "=", "master_plan.id_ws")->
                leftJoin(DB::raw("(
                    SELECT
                        master_plan.id_ws,
                        output_rfts".($this->outputType).".master_plan_id,
                        userpassword.username sewing_line
                    FROM
                        output_rfts".($this->outputType)."
                        ".($this->outputType != "_packing" ?
                        "LEFT JOIN user_sb_wip ON user_sb_wip.id = output_rfts".($this->outputType).".created_by LEFT JOIN userpassword ON userpassword.line_id = user_sb_wip.line_id" :
                        "LEFT JOIN userpassword ON userpassword.username = output_rfts".($this->outputType).".created_by")."
                        LEFT JOIN master_plan on master_plan.id = output_rfts".($this->outputType).".master_plan_id
                    WHERE
                        output_rfts".($this->outputType).".created_by IS NOT NULL
                    GROUP BY
                        output_rfts".($this->outputType).".master_plan_id,
                        output_rfts".($this->outputType).".created_by
                ) as rfts"), function ($join) {
                    $join->on("rfts.master_plan_id", "=", "master_plan.id");
                });
                if ($this->dateFromFilter) {
                    $orderFilterSql->where('master_plan.tgl_plan', '>=', $this->dateFromFilter);
                }
                if ($this->dateToFilter) {
                    $orderFilterSql->where('master_plan.tgl_plan', '<=', $this->dateToFilter);
                }
                if ($this->groupBy == "size") {
                    $orderFilterSql->leftJoin('so', 'so.id_cost', '=', 'act_costing.id')->leftJoin('so_det', function ($join) { $join->on('so_det.id_so', '=', 'so.id'); $join->on('so_det.color', '=', 'master_plan.color'); });
                }
                if ($this->groupBy == "size" && $this->sizeFilter) {
                    $orderFilterSql->where('so_det.size', $this->sizeFilter);
                }
                $orderFilterSql->
                    where("act_costing.id", $this->selectedOrder)->
                    groupByRaw("master_plan.id_ws, act_costing.styleno, master_plan.color, COALESCE(rfts.sewing_line, master_plan.sewing_line) ".($this->groupBy == "size" ? ", so_det.size" : "")."")->
                    orderBy("master_plan.id_ws", "asc")->
                    orderBy("act_costing.styleno", "asc")->
                    orderBy("master_plan.color", "asc")->
                    orderByRaw("COALESCE(rfts.sewing_line, master_plan.sewing_line) asc ".($this->groupBy == 'size' ? ', so_det.id asc' : ''));

                $this->orderFilter = $orderFilterSql->get();

            $dailyOrderGroupSql = MasterPlan::selectRaw("
                    master_plan.tgl_plan tanggal,
                    act_costing.kpno ws,
                    act_costing.styleno style,
                    master_plan.color,
                    COALESCE(rfts.sewing_line, master_plan.sewing_line) as sewing_line
                    ".($this->groupBy == "size" ? ", so_det.id as so_det_id, so_det.size, (CASE WHEN so_det.dest is not null AND so_det.dest != '-' THEN CONCAT(so_det.size, ' - ', so_det.dest) ELSE so_det.size END) sizedest" : "")."
                ")->
                leftJoin("act_costing", "act_costing.id", "=", "master_plan.id_ws")->
                leftJoin(DB::raw("(
                    SELECT
                        master_plan.id_ws,
                        output_rfts".($this->outputType).".master_plan_id,
                        userpassword.username sewing_line
                    FROM
                        output_rfts".($this->outputType)."
                        ".($this->outputType != "_packing" ?
                        "LEFT JOIN user_sb_wip ON user_sb_wip.id = output_rfts".($this->outputType).".created_by LEFT JOIN userpassword ON userpassword.line_id = user_sb_wip.line_id" :
                        "LEFT JOIN userpassword ON userpassword.username = output_rfts".($this->outputType).".created_by")."
                        LEFT JOIN master_plan on master_plan.id = output_rfts".($this->outputType).".master_plan_id
                    WHERE
                        output_rfts".($this->outputType).".created_by IS NOT NULL
                    GROUP BY
                        output_rfts".($this->outputType).".master_plan_id,
                        output_rfts".($this->outputType).".created_by
                ) as rfts"), function ($join) {
                    $join->on("rfts.master_plan_id", "=", "master_plan.id");
                });
                if ($this->groupBy == "size") {
                    $dailyOrderGroupSql->leftJoin('so', 'so.id_cost', '=', 'act_costing.id')->leftJoin('so_det', function ($join) { $join->on('so_det.id_so', '=', 'so.id'); $join->on('so_det.color', '=', 'master_plan.color'); });
                }
                if ($this->dateFromFilter) {
                    $dailyOrderGroupSql->where('master_plan.tgl_plan', '>=', $this->dateFromFilter);
                }
                if ($this->dateToFilter) {
                    $dailyOrderGroupSql->where('master_plan.tgl_plan', '<=', $this->dateToFilter);
                }
                if ($this->colorFilter) {
                    $dailyOrderGroupSql->where('master_plan.color', $this->colorFilter);
                }
                if ($this->lineFilter) {
                    $dailyOrderGroupSql->where('rfts.sewing_line', $this->lineFilter);
                }
                if ($this->groupBy == "size" && $this->sizeFilter) {
                    $dailyOrderGroupSql->where('so_det.size', $this->sizeFilter);
                }
                $dailyOrderGroupSql->
                    where("act_costing.id", $this->selectedOrder)->
                    groupByRaw("master_plan.id_ws, act_costing.styleno, master_plan.color, COALESCE(rfts.sewing_line, master_plan.sewing_line) ".($this->groupBy == "size" ? ", so_det.size" : "")."")->
                    orderBy("master_plan.id_ws", "asc")->
                    orderBy("act_costing.styleno", "asc")->
                    orderBy("master_plan.color", "asc")->
                    orderByRaw("COALESCE(rfts.sewing_line, master_plan.sewing_line) asc ".($this->groupBy == 'size' ? ', so_det.id asc' : ''));

                $this->dailyOrderGroup = $dailyOrderGroupSql->get();

            $masterPlanDateFilter = " between '".$this->dateFromFilter."' and '".$this->dateToFilter."'";
            $masterPlanDateFilter1 = " between '".date('Y-m-d', strtotime('-1 days', strtotime($this->dateFromFilter)))."' and '".$this->dateToFilter."'";

            $dailyOrderOutputSql = MasterPlan::selectRaw("
                    rfts.tanggal,
                    ".($this->groupBy == 'size' ? ' rfts.so_det_id, so_det.size, ' : '')."
                    SUM( rfts.rft ) output,
                    act_costing.kpno ws,
                    act_costing.styleno style,
                    master_plan.color,
                    COALESCE ( rfts.created_by, master_plan.sewing_line ) AS sewing_line,
                    master_plan.smv smv,
                    master_plan.jam_kerja jam_kerja,
                    master_plan.man_power man_power,
                    master_plan.plan_target plan_target,
                    COALESCE ( rfts.last_rft, master_plan.tgl_plan ) latest_output
                ")->
                join(DB::raw("
                    (
                        SELECT
                            coalesce( date( rfts.updated_at ), master_plan.tgl_plan ) tanggal,
                            max( rfts.updated_at ) last_rft,
                            count( rfts.id ) rft,
                            master_plan.id master_plan_id,
                            master_plan.id_ws master_plan_id_ws,
                            COALESCE ( userpassword.username, master_plan.sewing_line ) created_by
                            ".($this->groupBy == 'size' ? ', rfts.so_det_id ' : '')."
                        FROM
                            output_rfts".$this->outputType." rfts
                            INNER JOIN master_plan ON master_plan.id = rfts.master_plan_id ".
                            (
                                $this->outputType != " _packing " ? "
                                LEFT JOIN user_sb_wip ON user_sb_wip.id = rfts.created_by
                                LEFT JOIN userpassword ON userpassword.line_id = user_sb_wip.line_id " : "
                                LEFT JOIN userpassword ON userpassword.username = rfts.created_by "
                            )."
                        WHERE
                            DATE ( rfts.updated_at ) ".$masterPlanDateFilter."
                            AND master_plan.tgl_plan ".$masterPlanDateFilter1."
                            AND master_plan.id_ws = ".$this->selectedOrder."
                        GROUP BY
                            master_plan.id_ws,
                            DATE ( rfts.updated_at ),
                            COALESCE ( userpassword.username, master_plan.sewing_line )
                            ".($this->groupBy == 'size' ? ', rfts.so_det_id ' : '')."
                    ) rfts
                "), "rfts.master_plan_id", "=", "master_plan.id")->
                leftJoin("act_costing", "act_costing.id", "=", "master_plan.id_ws");

                if ($this->groupBy == "size") {
                    $dailyOrderOutputSql->leftJoin('so_det', 'so_det.id', '=', 'rfts.so_det_id');
                }

                $dailyOrderOutputSql->
                    where("act_costing.id", $this->selectedOrder)->
                    groupByRaw("master_plan.id_ws, act_costing.styleno, master_plan.color, COALESCE(rfts.created_by, master_plan.sewing_line) , master_plan.tgl_plan ".($this->groupBy == 'size' ? ', so_det.size' : '')."")->
                    orderBy("master_plan.id_ws", "asc")->
                    orderBy("act_costing.styleno", "asc")->
                    orderBy("master_plan.color", "asc")->
                    orderByRaw("COALESCE(rfts.created_by, master_plan.sewing_line) asc ".($this->groupBy == 'size' ? ', so_det.id asc' : ''));
                if ($this->dateFromFilter) {
                    $dailyOrderOutputSql->whereRaw('rfts.tanggal', $this->dateFromFilter);
                }
                if ($this->dateToFilter) {
                    $dailyOrderOutputSql->whereRaw('rfts.tanggal', $this->dateToFilter);
                }
                if ($this->colorFilter) {
                    $dailyOrderOutputSql->where('master_plan.color', $this->colorFilter);
                }
                if ($this->lineFilter) {
                    $dailyOrderOutputSql->whereRaw('COALESCE(rfts.created_by, master_plan.sewing_line) = "'.$this->lineFilter.'"');
                }
                if ($this->groupBy == "size" && $this->sizeFilter) {
                    $dailyOrderOutputSql->where('so_det.size', $this->sizeFilter);
                }
                $this->dailyOrderOutputs = $dailyOrderOutputSql->get();

            \Log::info("Query Completed");
        }

        return view('livewire.track-order-output');
    }
}

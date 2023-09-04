<?php

namespace App\Http\Controllers;

use App\Models\Marker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $markers = Marker::all();

        if ($request->ajax()) {
            $markers = Marker::all();

            return $markers;
        }

        return view('marker.marker');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $orders = DB::connection('mysql_sb')->
            table('act_costing')->
            select('id', 'kpno')->
            where('status', '!=', 'CANCEL')->
            where('cost_date', '>=', '2023-01-01')->
            where('type_ws', 'STD')->
            orderBy('cost_date', 'desc')->
            orderBy('kpno', 'asc')->
            groupBy('kpno')->
            get();

        return view('marker.create-marker', ['orders' => $orders]);
    }

    public function getOrderInfo(Request $request)
    {
        $order = DB::connection('mysql_sb')->
            table('act_costing')->
            selectRaw('act_costing.id, act_costing.kpno, act_costing.styleno, act_costing.qty order_qty, mastersupplier.supplier buyer')->
            leftJoin('mastersupplier', 'mastersupplier.Id_Supplier', '=', 'act_costing.id_buyer')->
            where('id', $request->act_costing_id)->
            first();

        return json_encode($order);
    }

    public function getColorList(Request $request)
    {
        $colors = DB::connection('mysql_sb')->
            select("select sd.color from so_det sd
            inner join so on sd.id_so = so.id
            inner join act_costing ac on so.id_cost = ac.id
            where ac.id = '".$request->act_costing_id."' and sd.cancel = 'N'
            group by sd.color");

        $html = "<option value=''>Pilih Color</option>";

        foreach ($colors as $color) {
            $html .= " <option value='".$color->color."'>".$color->color."</option> ";
        }

        return $html;
    }

    public function getSizeList(Request $request)
    {
        $sizes = DB::connection('mysql_sb')->
            select("
                select sd.id, ac.kpno no_ws, sd.color, sd.qty order_qty, sd.size from so_det sd
                    inner join so on sd.id_so = so.id
                    inner join act_costing ac on so.id_cost = ac.id
                    inner join master_size_new msn on sd.size = msn.size
                where ac.id = '".$request->act_costing_id."' and sd.color = '".$request->color."' and sd.cancel = 'N'
                group by sd.size
                order by msn.urutan asc
            ");

        return json_encode([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval(count($sizes)),
            "recordsFiltered" => intval(count($sizes)),
            "data" => $sizes
        ]);
    }

    public function getPanelList(Request $request)
    {
        $panels = DB::connection('mysql_sb')->
            select("
                select nama_panel panel from
                    (select id_panel from bom_jo_item k
                        inner join so_det sd on k.id_so_det = sd.id
                        inner join so on sd.id_so = so.id
                        inner join act_costing ac on so.id_cost = ac.id
                        inner join masteritem mi on k.id_item = mi.id_gen
                        where ac.id = '".$request->act_costing_id."' and sd.color = '".$request->color."' and k.status = 'M'
                        and k.cancel = 'N' and sd.cancel = 'N' and so.cancel_h = 'N' and ac.status = 'confirm' and mi.mattype = 'F'
                        group by id_panel
                    )a
                inner join masterpanel mp on a.id_panel = mp.id
            ");

        $html = "<option value=''>Pilih Panel</option>";

        foreach ($panels as $panel) {
            $html .= " <option value='".$panel->panel."'>".$panel->panel."</option> ";
        }

        return $html;
    }

    public function getNumber(Request $request) {
        $number = DB::connection('mysql_sb')->
            select("
                select k.cons cons_ws,sum(sd.qty) order_qty from bom_jo_item k
                inner join so_det sd on k.id_so_det = sd.id
                inner join so on sd.id_so = so.id
                inner join act_costing ac on so.id_cost = ac.id
                inner join masteritem mi on k.id_item = mi.id_gen
                inner join masterpanel mp on k.id_panel = mp.id
                where ac.id = '".$request->act_costing_id."' and sd.color = '".$request->color."' and mp.nama_panel ='".$request->panel."' and k.status = 'M'
                and k.cancel = 'N' and sd.cancel = 'N' and so.cancel_h = 'N' and ac.status = 'confirm' and mi.mattype = 'F'
                group by sd.color, k.id_item, k.unit
                limit 1
            ");

        return json_encode($number[0]);
    }


    public function getCount(Request $request) {
        $countMarker = Marker::where('act_costing_id', $request->act_costing_id)->
            where('color', $request->color)->
            where('panel', $request->panel)->
            count() + 1;

        return $countMarker ? $countMarker : 1;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request);

        $validatedRequest = $request->validate([
            'tgl_cutting' => ['required'],
            'ws' => ['required'],
            'color' => ['required'],
            'panel' => ['required'],
            'p_marker' => ['required', 'numeric'],
            'p_unit' => ['required'],
            'comma_marker ' => ['required', 'numeric'],
            'comma_unit ' => ['required'],
            'l_marker ' => ['required', 'numeric'],
            'l_unit ' => ['required'],
            'cons_marker ' => ['required', 'numeric'],
            'gelar_marker_qty ' => ['required', 'numeric'],
            'po ' => ['required'],
            'no_urut_marker ' => ['required', 'numeric'],
        ]);

        $markers = Marker::all();

        $markerCode = 'MRK/'.date('ym').'/'.sprintf('%05s', $validatedRequest['no_urut_marker']);

        $markerStore = Marker::create([
            'kode' => $markerCode,
            'act_costing_id' => $validatedRequest['ws'],
            'color' => $validatedRequest['color'],
            'panel' => $validatedRequest['panel'],
            'panjang_marker' => $validatedRequest['p_marker'],
            'unit_panjang_marker' => $validatedRequest['p_unit'],
            'comma_marker' => $validatedRequest['comma_marker'],
            'unit_comma_marker' => $validatedRequest['comma_unit'],
            'lebar_marker' => $validatedRequest['l_marker'],
            'unit_lebar_marke ' => $validatedRequest['l_unit'],
            'gelar_qty' => $validatedRequest['gelar_marker_qty'],
            'po_marker' => $validatedRequest['po'],
            'urut_marker ' => $validatedRequest['no_urut_marker'],
        ]);

        $markerDetailStore = Marker::create([
            'marker_id' => $markerStore->id,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Marker  $marker
     * @return \Illuminate\Http\Response
     */
    public function show(Marker $marker)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marker  $marker
     * @return \Illuminate\Http\Response
     */
    public function edit(Marker $marker)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Marker  $marker
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Marker $marker)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Marker  $marker
     * @return \Illuminate\Http\Response
     */
    public function destroy(Marker $marker)
    {
        //
    }
}

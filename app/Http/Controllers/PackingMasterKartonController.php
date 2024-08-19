<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Illuminate\Support\Facades\Auth;

class PackingMasterKartonController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user()->name;
        $tgl_awal = $request->dateFrom;
        $tgl_akhir = $request->dateTo;
        $user = Auth::user()->name;
        if ($request->ajax()) {
            $additionalQuery = '';
            $data_carton = DB::select("
SELECT
a.po,
b.ws,
b.buyer,
b.styleno,
b.product_group,
b.product_item,
concat((DATE_FORMAT(b.tgl_shipment,  '%d')), '-', left(DATE_FORMAT(b.tgl_shipment,  '%M'),3),'-',DATE_FORMAT(b.tgl_shipment,  '%Y')) tgl_shipment_fix,
tot_karton,
tot_karton_isi,
tot_karton_kosong,
coalesce(s.tot_scan,0) tot_scan
from
  (
select a.po,
count(a.no_carton)tot_karton,
count(IF(b.no_carton is not null,1,null)) tot_karton_isi,
count(IF(b.no_carton is null,1,null)) tot_karton_kosong
from  packing_master_carton a
left join (
select no_carton, po from packing_packing_out_scan group by no_carton,po  ) b on
a.po = b.po and  a.no_carton = b.no_carton
group by a.po
) a
left join
(
select
p.po,
m.ws,
m.styleno,
tgl_shipment,
m.buyer,
m.product_group,
m.product_item
from ppic_master_so p
inner join master_sb_ws m on p.id_so_det = m.id_so_det
group by po
) b on a.po = b.po
left join
(select po,count(barcode) tot_scan from packing_packing_out_scan group by po) s on a.po = s.po
where tgl_shipment >= '$tgl_awal' and tgl_shipment <= '$tgl_akhir'
 order by tgl_shipment asc, po asc
          ");

            //   SELECT
            //   a.po,
            //   b.ws,
            //   b.buyer,
            //   b.styleno,
            //   b.product_group,
            //   b.product_item,
            //   concat((DATE_FORMAT(b.tgl_shipment,  '%d')), '-', left(DATE_FORMAT(b.tgl_shipment,  '%M'),3),'-',DATE_FORMAT(b.tgl_shipment,  '%Y')) tgl_shipment_fix,
            //   tot_carton,
            //   coalesce(s.tot_scan,0) tot_scan
            //   from
            //     (
            //      SELECT po,count(no_carton) tot_carton
            //      FROM `packing_master_carton`
            //      group by po) a
            //   left join (
            //   select
            //   p.po,
            //   m.ws,
            //   m.styleno,
            //   tgl_shipment,
            //   m.buyer,
            //   m.product_group,
            //   m.product_item
            //   from ppic_master_so p
            //   inner join master_sb_ws m on p.id_so_det = m.id_so_det
            //   ) b on a.po = b.po
            //   left join
            //   (select po,count(barcode) tot_scan from packing_packing_out_scan group by po) s on a.po = s.po
            //    where tgl_shipment >= '$tgl_awal' and tgl_shipment <= '$tgl_akhir'
            //    group by po
            //   order by tgl_shipment asc, po asc


            return DataTables::of($data_carton)->toJson();
        }

        $data_po = DB::select("SELECT po isi, po tampil from ppic_master_so group by po order by po asc");


        return view(
            'packing.packing_master_karton',
            [
                'page' => 'dashboard-packing',
                "subPageGroup" => "packing-master-karton",
                "subPage" => "master-karton",
                "data_po" => $data_po,
                "user" => $user,
            ]
        );
    }

    public function store(Request $request)
    {
        $user = Auth::user()->name;
        $timestamp = Carbon::now();
        $po = $request->cbopo;
        $tot_skrg = $request->tot_skrg;
        $tot_skrg_hit = $tot_skrg + 1;
        $tot_input = $request->txtinput_carton;
        $total = $tot_skrg + $tot_input;

        for ($i = $tot_skrg_hit; $i <= $total; $i++) {

            $cek = DB::select(
                "select count(id) id from packing_master_carton where po = '$po' and no_carton = '$i'"
            );
            $cek_data = $cek[0]->id;
            if ($cek_data != '1') {
                $insert = DB::insert(
                    "insert into packing_master_carton
                        (po,no_carton,created_at,updated_at,created_by) values
                        ('$po','$i','$timestamp','$timestamp','$user')
                        "
                );
            } else {
                return array(
                    "status" => 201,
                    "message" => 'Data Sudah Ada',
                    "additional" => [],
                );
            }
        }

        if ($insert) {
            return array(
                "status" => 200,
                "message" => 'Data Berhasil Di Upload',
                "additional" => [],
            );
        }

        // }
    }

    public function show_tot(Request $request)
    {
        $data_header = DB::select("
        SELECT coalesce(max(no_carton),0)tot_skrg
        FROM `packing_master_carton` where po = '$request->cbopo'
        ");

        return json_encode($data_header ? $data_header[0] : null);
    }

    public function show_detail_karton(Request $request)
    {
        $po = $request->po;

        $data_det_karton = DB::select("SELECT
mc.no_carton,
mc.po,
m.buyer,
dc.barcode,
m.ws,
m.color,
m.size,
p.dest,
p.desc,
m.styleno,
m.product_group,
m.product_item,
coalesce(dc.tot,'0') tot,
if (mc.po = dc.po,'isi','kosong')stat
from
(select * from packing_master_carton a where po = '$po')mc
left join
(
select count(barcode) tot, po, barcode, no_carton  from packing_packing_out_scan
where po = '$po'
group by po, no_carton, barcode, po
) dc on mc.po = dc.po and mc.no_carton = dc.no_carton
left join ppic_master_so p on dc.po = p.po and dc.barcode = p.barcode
left join master_sb_ws m on p.id_so_det = m.id_so_det
                    ");
        return DataTables::of($data_det_karton)->toJson();
    }

    public function getno_carton_hapus(Request $request)
    {
        $tgl_skrg = date('Y-m-d');
        $user = Auth::user()->name;
        $data_karton = DB::select("SELECT p.no_carton isi, concat (p.no_carton, ' ( ', coalesce(tot,0) , ' )') tampil
from
(
select * from packing_master_carton where po = '" . $request->txtmodal_h_po . "'
) p
left join
(
SELECT count(barcode) tot,po, no_carton from packing_packing_out_scan where po = '" . $request->txtmodal_h_po . "' group by po, no_carton
) o on p.po = o.po and p.no_carton = o.no_carton
        ");

        $html = "<option value=''>Pilih No Karton</option>";

        foreach ($data_karton as $datakarton) {
            $html .= " <option value='" . $datakarton->isi . "'>" . $datakarton->tampil . "</option> ";
        }

        return $html;
    }

    public function list_data_no_carton(Request $request)
    {
        $user = Auth::user()->name;
        $tgl_skrg = date('Y-m-d');
        $po = $request->po;
        $no_carton = $request->no_carton;
        $data_list = DB::select("SELECT
a.id,
a.barcode,
a.po,
a.dest,
p.desc,
m.color,
m.size,
m.ws,
a.no_carton
from packing_packing_out_scan a
inner join ppic_master_so p on a.barcode = p.barcode and a.po = p.po
inner join master_sb_ws m on p.id_so_det = m.id_so_det
where a.po = '$po' and a.no_carton = '$no_carton'
            ");

        return DataTables::of($data_list)->toJson();
    }

    public function hapus_master_karton_det(Request $request)
    {

        $timestamp = Carbon::now();
        $user = Auth::user()->name;
        $JmlArray                                   = $_POST['cek_data'];
        $po                                  = $_POST['txtmodal_h_po'];

        foreach ($JmlArray as $key => $value) {
            if ($value != '') {
                $txtid                          = $JmlArray[$key]; {
                    $ins_history =  DB::insert("
                    insert into packing_packing_out_scan_log (id_packing_Packing_out_scan, tgl_trans, barcode, po, no_carton, created_at, updated_at, created_by)
                    SELECT id, tgl_trans, barcode, po, no_carton,created_at, '$timestamp', '$user'  FROM `packing_packing_out_scan` where id = '$txtid'");

                    $del_history =  DB::delete("
                    delete from packing_packing_out_scan where id = '$txtid'");
                }
            }
        }
        return array(
            "status" => 201,
            "message" => 'Data Sudah di Hapus',
            "additional" => [],
            "redirect" => '',
            "table" => 'datatable_hapus',
            "callback" => "show_data_edit_h(`$po`)"
        );

        // return array(
        //     "status" => 202,
        //     "message" => 'No Form Berhasil Di Update',
        //     "additional" => [],
        //     "redirect" => '',
        //     "callback" => "getdetail(`$no_form_modal`,`$txtket_modal_input`)"

        // );
    }

    public function getno_carton_tambah(Request $request)
    {
        $tgl_skrg = date('Y-m-d');
        $user = Auth::user()->name;
        $data_karton = DB::select("SELECT p.no_carton isi, concat (p.no_carton, ' ( ', coalesce(tot,0) , ' )') tampil
from
(
select * from packing_master_carton where po = '" . $request->txtmodal_p_po . "'
) p
left join
(
SELECT count(barcode) tot,po, no_carton from packing_packing_out_scan where po = '" . $request->txtmodal_p_po . "' group by po, no_carton
) o on p.po = o.po and p.no_carton = o.no_carton
        ");

        $html = "<option value=''>Pilih No Karton</option>";

        foreach ($data_karton as $datakarton) {
            $html .= " <option value='" . $datakarton->isi . "'>" . $datakarton->tampil . "</option> ";
        }

        return $html;
    }

    public function getbarcode_tambah(Request $request)
    {
        $tgl_skrg = date('Y-m-d');
        $user = Auth::user()->name;
        $data_barcode = DB::select("SELECT barcode isi, barcode tampil
        from ppic_master_so where po = '" . $request->txtmodal_p_po . "'
        ");

        $html = "<option value=''>Pilih Barcode</option>";

        foreach ($data_barcode as $databarcode) {
            $html .= " <option value='" . $databarcode->isi . "'>" . $databarcode->tampil . "</option> ";
        }

        return $html;
    }

    public function getdest_tambah(Request $request)
    {
        $tgl_skrg = date('Y-m-d');
        $user = Auth::user()->name;
        $data_dest = DB::select("SELECT dest isi, dest tampil
        from ppic_master_so where po = '" . $request->txtmodal_p_po . "' and barcode  = '" . $request->cbomodal_p_barcode . "'
        ");

        $html = "<option value=''>Pilih Dest</option>";

        foreach ($data_dest as $datadest) {
            $html .= " <option value='" . $datadest->isi . "'>" . $datadest->tampil . "</option> ";
        }

        return $html;
    }

    public function list_data_no_carton_tambah(Request $request)
    {
        $user = Auth::user()->name;
        $tgl_skrg = date('Y-m-d');
        $po = $request->po;
        $no_carton = $request->no_carton;
        $data_list = DB::select("SELECT
a.*,
m.color,
m.size,
m.ws
from
(
SELECT barcode, po, dest, count(barcode)tot
from packing_packing_out_scan where po = '$po' and no_carton = '$no_carton'
group by barcode, po, dest
) a
inner join ppic_master_so p on a.barcode = p.barcode and a.po = p.po
inner join master_sb_ws m on p.id_so_det = m.id_so_det
            ");

        return DataTables::of($data_list)->toJson();
    }

    public function store_tambah_data_karton_det(Request $request)
    {
        $user = Auth::user()->name;
        $timestamp = Carbon::now();
        $tgl_skrg = date('Y-m-d');

        $po = $request->txtmodal_p_po;
        $barcode = $request->cbomodal_p_barcode;
        $qty = $request->cbomodal_p_qty;
        $dest = $request->cbomodal_p_dest;
        $no_carton = $request->cbomodal_p_no_karton;
        $stok = $request->cbomodal_p_qty_stok;

        $validatedRequest = $request->validate([
            "cbomodal_p_barcode" => "required",
            "cbomodal_p_qty" => "required",
            "cbomodal_p_dest" => "required",
            "cbomodal_p_no_karton" => "required",
        ]);

        if ($stok >= $qty) {
            for ($i = 1; $i <= $qty; $i++) {
                $insert = DB::insert("
                insert into packing_packing_out_scan
                (tgl_trans,barcode,po,dest,no_carton,created_by,created_at,updated_at)
                values
                (
                    '$tgl_skrg',
                    '$barcode',
                    '$po',
                    '$dest',
                    '$no_carton',
                    '$user',
                    '$timestamp',
                    '$timestamp'
                )
                ");
            }
            return array(
                'icon' => 'benar',
                'msg' => 'Data Sudah Terupdate',
            );
        } else {
            return array(
                'icon' => 'salah',
                'msg' => 'Tidak ada yang disimpan',
            );
        }
    }

    public function get_data_stok_packing_in(Request $request)
    {
        $cek_stok = DB::select("
        select coalesce(pack_in.tot_in,0)  - coalesce(pack_out.tot_out,0) tot_s
        from ppic_master_so p
        left join
        (
            select sum(qty) tot_in, id_ppic_master_so from packing_packing_in
            where barcode = '$request->barcode' and po = '$request->po' and dest = '$request->dest'
            group by id_ppic_master_so
        ) pack_in on p.id = pack_in.id_ppic_master_so
        left join
        (
            select count(p.barcode) tot_out, p.id
            from packing_packing_out_scan a
            inner join ppic_master_so p on a.barcode = p.barcode and a.po = p.po and a.dest = p.dest
            where p.barcode = '$request->barcode' and p.po = '$request->po' and p.dest = '$request->dest'
            group by a.barcode, a.po
        ) pack_out on p.id = pack_out.id
        where p.barcode = '$request->barcode' and p.po = '$request->po' and dest = '$request->dest'
        ");
        return json_encode($cek_stok ? $cek_stok[0] : null);
    }
}

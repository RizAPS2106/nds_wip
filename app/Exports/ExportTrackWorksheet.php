<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
    $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class ExportTrackWorksheet implements FromView, WithEvents, /*WithColumnWidths,*/ ShouldAutoSize
{
    use Exportable;

    protected $month, $year;

    public function __construct($month, $year)
    {
        $this->month = $month ? $month : date('m');
        $this->year = $year ? $year : date('Y');
        $this->rowCount = 0;
    }

    public function view(): View
    {
        $worksheet = DB::select("
            select
                DATE(master_sb_ws.tgl_kirim) tgl_kirim,
                master_sb_ws.id_act_cost,
                master_sb_ws.ws,
                master_sb_ws.styleno,
                master_sb_ws.color,
                master_sb_ws.id_so_det,
                master_sb_ws.size,
                master_sb_ws.dest,
                master_sb_ws.qty,
                marker_track.kode,
                marker_track.panel,
                sum(marker_track.total_gelar_marker) total_gelar_marker,
                sum(marker_track.total_ratio_marker) total_ratio_marker,
                sum(marker_track.total_cut_marker) total_cut_marker,
                sum(marker_track.total_lembar_form) total_lembar_form,
                sum(marker_track.total_cut_form) total_cut_form,
                sum(marker_track.total_stocker) total_stocker,
                sum(marker_track.total_dc) total_dc,
                sum(marker_track.total_sec) total_sec,
                sum(marker_track.total_sec_in) total_sec_in
            from
                master_sb_ws
            left join
                (
                select
                    marker.id,
                    marker.act_costing_id,
                    marker.kode,
                    marker.panel,
                    marker_detail.so_det_id,
                    marker.gelar_qty total_gelar_marker,
                    marker_detail.ratio total_ratio_marker,
                    marker_detail.cut_qty total_cut_marker,
                    form_cut.qty_ply total_lembar_form,
                    sum(marker_detail.ratio * form_cut.qty_ply) total_cut_form,
                    sum(stocker.qty_ply) total_stocker,
                    sum(stocker.dc_qty_ply) total_dc,
                    sum(stocker.sec_qty_ply) total_sec,
                    sum(stocker.sec_in_qty_ply) total_sec_in
                from
                    marker_input marker
                left join
                    (
                        select
                            marker_input_detail.marker_id,
                            marker_input_detail.so_det_id,
                            marker_input_detail.size,
                            sum(marker_input_detail.ratio) ratio,
                            sum(marker_input_detail.cut_qty) cut_qty
                        from
                            marker_input_detail
                        where
                            marker_input_detail.ratio > 0
                        group by
                            marker_id,
                            so_det_id
                    ) marker_detail on marker_detail.marker_id = marker.id
                left join
                    (
                        select
                            form_cut_input.id,
                            form_cut_input.id_marker,
                            form_cut_input.no_form,
                            sum(coalesce(form_cut_input.total_lembar, form_cut_input.qty_ply)) qty_ply
                        from
                            form_cut_input
                        where
                            form_cut_input.qty_ply is not null and form_cut_input.id_marker is not null
                        group by
                            form_cut_input.id_marker
                    ) form_cut on form_cut.id_marker = marker.kode
                left join
                    (
                        select
                            *
                        from
                        (
                            select
                                stocker_input.form_cut_id,
                                stocker_input.part_detail_id,
                                stocker_input.so_det_id,
                                sum(coalesce(stocker_input.qty_ply_mod, stocker_input.qty_ply)) qty_ply,
                                sum((dc_in_input.qty_awal - dc_in_input.qty_reject + dc_in_input.qty_replace)) dc_qty_ply,
                                sum(secondary_in_input.qty_in) sec_qty_ply,
                                sum(secondary_inhouse_input.qty_in) sec_in_qty_ply
                            from
                                stocker_input
                                inner join dc_in_input on dc_in_input.id_qr_stocker = stocker_input.id_qr_stocker
                                left join secondary_in_input on secondary_in_input.id_qr_stocker = dc_in_input.id_qr_stocker
                                left join secondary_inhouse_input on secondary_inhouse_input.id_qr_stocker = secondary_in_input.id_qr_stocker
                            group by
                                stocker_input.form_cut_id,
                                stocker_input.part_detail_id,
                                stocker_input.so_det_id
                        ) stocker
                        group by
                            stocker.form_cut_id,
                            stocker.so_det_id
                    ) stocker on stocker.form_cut_id = form_cut.id and stocker.so_det_id = marker_detail.so_det_id
                    group by
                        marker.id,
                        marker_detail.so_det_id
                ) marker_track on marker_track.act_costing_id = master_sb_ws.id_act_cost and marker_track.so_det_id = master_sb_ws.id_so_det
                where
                    MONTH( master_sb_ws.tgl_kirim ) = '".$this->month."' AND
                    YEAR( master_sb_ws.tgl_kirim ) = '".$this->year."'
                group by
                    master_sb_ws.id_so_det,
                    marker_track.panel
                order by
                    master_sb_ws.id_act_cost,
                    master_sb_ws.color,
                    master_sb_ws.id_so_det,
                    master_sb_ws.dest
            ");

        $this->rowCount = count($worksheet) + 3;

        return view('track.worksheet.export.worksheet', [
            'worksheet' => collect($worksheet),
            'month' => $this->month,
            'year' => $this->year
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => [self::class, 'afterSheet']
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $event->sheet->styleCells(
            'A3:P' . $event->getConcernable()->rowCount,
            [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ]
        );
    }

    // public function columnWidths(): array
    // {
    //     return [
    //         'A' => 15,
    //         'C' => 15,
    //         'D' => 15,
    //         'E' => 15,
    //         'G' => 25,
    //     ];
    // }
}
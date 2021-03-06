<?php
use app\controllers\Controller;
use app\models\ProgramModel;
use app\models\ActivityModel;
use app\models\PerformanceReportModel;
use app\models\PackageModel;
use app\models\PackageDetailModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PerformanceReportController extends Controller
{
    public $pageHead = [
        'LAPORAN CAPAIAN KINERJA BULANAN',
        'BIDANG BINA MARGA DPU KAB. SEMARANG',
        'THN ANGGARAN:',
        'BULAN:'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->setControllerAttribute(__CLASS__);
        $this->title = 'Capaian Kinerja Bulanan';
        $this->smarty->assign('title', $this->title);
        $this->performanceReportModel = new PerformanceReportModel();
        $this->pageHead[2] = "{$this->pageHead[2]} {$_POST['fiscal_year']}";
        $this->pageHead[3] = "{$this->pageHead[3]} {$_POST['fiscal_month']}";

        if (!$_SESSION['USER']['usr_is_report']) {
            header('Location:' . BASE_URL . '/403');
        }
    }

    public function index()
    {
        $programModel = new ProgramModel();
        list($program) = $programModel->multiarray(null, [['prg_name', 'ASC']]);

        $activityModel = new ActivityModel();
        list($activity) = $activityModel->multiarray(null, [
            ['act_name', 'ASC']
        ]);

        $this->smarty->assign('breadcrumb', [
            ['Laporan', ''],
            [$this->title, '']
        ]);

        $this->smarty->assign('subtitle', "Laporan {$this->title}");
        $this->smarty->assign('program', $program);
        $this->smarty->assign('activity', $activity);

        $this->smarty->display("{$this->directory}/index.tpl");
    }

    public function search()
    {
        $list = $this->performanceReportModel->getData($_POST);

        echo json_encode([$list, $this->pageHead]);
        exit();
    }

    public function downloadSpreadsheet()
    {
        $colors = [
            'white' => 'FFFFFF',
            'red' => 'dc3545',
            'yellow' => 'ffc107',
            'green' => '28a745'
        ];

        $ext = $_POST['ext'] ?? 'xls';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $list = $this->performanceReportModel->getData($_POST);
        $list_count = count($list);

        $lastCol = 'M';

        $titles = $this->pageHead;

        for ($i = 1; $i <= 3; $i++) {
            $sheet->setCellValue("A{$i}", $titles[$i - 1]);
            $sheet->mergeCells("A{$i}:{$lastCol}{$i}");
        }

        $sheet->getStyle('A1:A3')->applyFromArray([
            'font' => [
                'bold' => true
            ],
            'alignment' => [
                'horizontal' =>
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' =>
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ]
        ]);

        $alphabet = range('A', $lastCol);
        if (($key = array_search('B', $alphabet)) !== false) {
            unset($alphabet[$key]);
        }
        $alphabet = array_values($alphabet);

        if ($list_count > 0) {
            foreach ($list as $rows) {
                if (is_null($rows['detail'])) {
                    continue;
                }
                $prg_row = $prg_row ?? 5;
                $act_row = $prg_row + 1;

                // $sheet->mergeCells("A{$prg_row}:B{$prg_row}");
                $sheet->setCellValue("A{$prg_row}", 'Program:');
                $sheet->setCellValue("B{$prg_row}", $rows['prg_name']);

                // $sheet->mergeCells("A{$act_row}:B{$act_row}");
                $sheet->setCellValue("A{$act_row}", 'Kegiatan:');
                $sheet->setCellValue("B{$act_row}", $rows['act_name']);

                $detail_head1 = $act_row + 1;
                $detail_head2 = $detail_head1 + 1;

                $sheet->mergeCells("A{$detail_head1}:B{$detail_head2}");

                $cols1 = range('C', 'F');
                foreach ($cols1 as $col) {
                    $sheet->mergeCells(
                        "{$col}{$detail_head1}:{$col}{$detail_head2}"
                    );
                }

                $cols2 = range('G', 'K', 2);
                $cols3 = range('H', 'L', 2);
                for ($i = 0; $i < 3; $i++) {
                    $sheet->mergeCells(
                        "{$cols2[$i]}{$detail_head1}:{$cols3[$i]}{$detail_head1}"
                    );
                }

                $cols1 = range('M', 'M');
                foreach ($cols1 as $col) {
                    $sheet->mergeCells(
                        "{$col}{$detail_head1}:{$col}{$detail_head2}"
                    );
                }
                // $sheet->getRowDimension($detail_head1)->setRowHeight(30);
                $sheet->getRowDimension($detail_head2)->setRowHeight(30);

                $sheet->fromArray(
                    [
                        'Paket Kegiatan',
                        '',
                        "Pagu Anggaran\n(Rp)",
                        "Nilai Awal Kontrak\n(Rp)",
                        "Nilai Kontrak Akhir\n(Rp)",
                        "Tanggal Periode\nTerakhir",
                        'Target',
                        '',
                        'Realisasi',
                        '',
                        'Deviasi',
                        '',
                        "Indi-\nkator"
                    ],
                    null,
                    "A{$detail_head1}"
                );
                $sheet->fromArray(
                    [
                        "Fisik\n(%)",
                        "Keuangan\n(%)",
                        "Fisik\n(%)",
                        "Keuangan\n(%)",
                        "Fisik\n(%)",
                        "Keuangan\n(%)"
                    ],
                    null,
                    "G{$detail_head2}"
                );

                $sheet
                    ->getStyle("A{$detail_head1}:{$lastCol}{$detail_head2}")
                    ->applyFromArray([
                        'font' => [
                            'bold' => true
                        ],
                        'alignment' => [
                            'horizontal' =>
                                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' =>
                                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                            ]
                        ]
                    ]);

                $n = 1;
                $no = 1;
                foreach ($rows['detail'] as $row) {
                    // var_dump($row);

                    $detail_body = $detail_head2 + $n;
                    $n++;

                    $sheet->mergeCells("A{$detail_body}:B{$detail_body}");

                    $content = [
                        $row['pkgd_name'],
                        $row['pkgd_debt_ceiling'],
                        $row['cnt_value'],
                        $row['cnt_value_end'],
                        $row['prog_date'],
                        $row['trg_physical'],
                        $row['trg_finance_pct'],
                        $row['prog_physical'],
                        $row['prog_finance_pct'],
                        $row['devn_physical'],
                        $row['devn_finance_pct']
                    ];

                    foreach ($alphabet as $key => $value) {
                        $sheet->setCellValue(
                            "{$value}{$detail_body}",
                            $content[$key]
                        );
                    }

                    $sheet
                        ->getStyle("M{$detail_body}")
                        ->getFill()
                        ->setFillType(
                            \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
                        )
                        ->getStartColor()
                        ->setARGB($colors[$row['indicator']]);

                    $sheet
                        ->getStyle("C{$detail_body}:E{$detail_body}")
                        ->applyFromArray([
                            'alignment' => [
                                'horizontal' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
                            ]
                        ]);

                    $sheet->getStyle("F{$detail_body}")->applyFromArray([
                        'alignment' => [
                            'horizontal' =>
                                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
                        ]
                    ]);

                    $sheet
                        ->getStyle("G{$detail_body}:L{$detail_body}")
                        ->applyFromArray([
                            'alignment' => [
                                'horizontal' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
                            ]
                        ]);
                }

                $sheet
                    ->getStyle("A{$detail_head2}:{$lastCol}{$detail_body}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                            ]
                        ]
                    ]);

                $prg_row = $detail_body + 2;
            }
        }

        foreach ($alphabet as $value) {
            $sheet->getColumnDimension($value)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $t = time();
        $filename = "Laporan-Capaian-Kinerja-Bulanan-{$t}.{$ext}";
        $filepath = "download/{$filename}";
        $writer->save(DOC_ROOT . $filepath);
        echo json_encode($filepath);
    }
}

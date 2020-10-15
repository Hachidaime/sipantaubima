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
    public function __construct()
    {
        parent::__construct();
        $this->setControllerAttribute(__CLASS__);
        $this->title = 'Capaian Kinerja Bulanan';
        $this->smarty->assign('title', $this->title);
        $this->performanceReportModel = new PerformanceReportModel();

        if (!$_SESSION['USER']['usr_is_report']) {
            header('Location:' . BASE_URL . '/403');
        }
    }

    public function index()
    {
        $programModel = new ProgramModel();
        list($program) = $programModel->multiarray(null, [['prg_code', 'ASC']]);

        $activityModel = new ActivityModel();
        list($activity) = $activityModel->multiarray(null, [
            ['act_code', 'ASC'],
        ]);

        $packageModel = new PackageModel();
        list($package, $packageCount) = $packageModel->multiarray();

        $packageDetail = [];
        if ($packageCount > 0) {
            $pkgIdList = implode(
                ',',
                array_map(function ($val) {
                    return $val['id'];
                }, $package),
            );
            $packageDetailModel = new PackageDetailModel();
            $query = "SELECT * FROM `{$packageDetailModel->getTable()}` 
                WHERE `pkg_id` IN ($pkgIdList) 
                ORDER BY `pkgd_name` ASC";
            $packageDetail = $packageDetailModel->db->query($query)->toArray();
        }

        $this->smarty->assign('breadcrumb', [
            ['Laporan', ''],
            [$this->title, ''],
        ]);

        $this->smarty->assign('subtitle', "Laporan {$this->title}");
        $this->smarty->assign('program', $program);
        $this->smarty->assign('activity', $activity);
        $this->smarty->assign('packageDetail', $packageDetail);

        $this->smarty->display("{$this->directory}/index.tpl");
    }

    public function search()
    {
        $list = $this->performanceReportModel->getData($_POST);

        echo json_encode($list);
        exit();
    }

    public function downloadSpreadsheet()
    {
        $colors = [
            'white' => 'FFFFFF',
            'red' => 'dc3545',
            'yellow' => 'ffc107',
            'green' => '28a745',
        ];

        $ext = $_POST['ext'] ?? 'xls';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $list = $this->performanceReportModel->getData($_POST);
        $list_count = count($list);

        $sheet->setCellValue('A1', 'LAPORAN CAPAIAN KINERJA BULANAN');
        $sheet->setCellValue('A2', 'BINA MARGA KAB. SEMARANG');
        $sheet->setCellValue('A3', "THN ANGGARAN: {$_POST['pkg_fiscal_year']}");

        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->mergeCells('A3:K3');

        $sheet->getStyle('A1:A3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' =>
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' =>
                    \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        if ($list_count > 0) {
            foreach ($list as $rows) {
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
                $sheet->mergeCells("C{$detail_head1}:C{$detail_head2}");
                $sheet->mergeCells("D{$detail_head1}:D{$detail_head2}");

                $sheet->mergeCells("E{$detail_head1}:F{$detail_head1}");
                $sheet->mergeCells("G{$detail_head1}:H{$detail_head1}");
                $sheet->mergeCells("I{$detail_head1}:J{$detail_head1}");
                $sheet->mergeCells("K{$detail_head1}:K{$detail_head2}");
                $sheet->getRowDimension($detail_head1)->setRowHeight(30);

                $sheet->fromArray(
                    [
                        'Paket Kegiatan',
                        '',
                        'Nilai Kontrak (Rp)',
                        "Tanggal Periode\nTerakhir",
                        'Target',
                        '',
                        'Realisasi',
                        '',
                        'Deviasi',
                        '',
                        "Indi-\nkator",
                    ],
                    null,
                    "A{$detail_head1}",
                );
                $sheet->fromArray(
                    [
                        'Fisik (%)',
                        'Keuangan (Rp)',
                        'Fisik (%)',
                        'Keuangan (Rp)',
                        'Fisik (%)',
                        'Keuangan (Rp)',
                    ],
                    null,
                    "E{$detail_head2}",
                );

                $sheet
                    ->getStyle("A{$detail_head1}:K{$detail_head2}")
                    ->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'alignment' => [
                            'horizontal' =>
                                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' =>
                                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                $n = 1;
                $no = 1;
                foreach ($rows['detail'] as $pkg) {
                    // var_dump($pkg);
                    foreach ($pkg as $idx => $row) {
                        $detail_body = $detail_head2 + $n;
                        $n++;

                        $sheet->mergeCells("A{$detail_body}:B{$detail_body}");

                        $sheet->setCellValue(
                            "A{$detail_body}",
                            "{$row['pkgd_name']}",
                        );

                        $sheet->setCellValue(
                            "C{$detail_body}",
                            $row['cnt_value'],
                        );

                        $sheet->setCellValue(
                            "D{$detail_body}",
                            $row['pkgd_last_prog_date'],
                        );

                        $sheet->setCellValue(
                            "E{$detail_body}",
                            $row['trg_physical'],
                        );
                        $sheet->setCellValue(
                            "F{$detail_body}",
                            $row['trg_finance'],
                        );

                        $sheet->setCellValue(
                            "G{$detail_body}",
                            $row['prog_physical'],
                        );
                        $sheet->setCellValue(
                            "H{$detail_body}",
                            $row['prog_finance'],
                        );

                        $sheet->setCellValue(
                            "I{$detail_body}",
                            $row['devn_physical'],
                        );
                        $sheet->setCellValue(
                            "J{$detail_body}",
                            $row['devn_finance'],
                        );

                        $sheet
                            ->getStyle("K{$detail_body}")
                            ->getFill()
                            ->setFillType(
                                \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            )
                            ->getStartColor()
                            ->setARGB($colors[$row['indicator']]);

                        $sheet->getStyle("C{$detail_body}")->applyFromArray([
                            'alignment' => [
                                'horizontal' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                            ],
                        ]);
                        $sheet
                            ->getStyle("E{$detail_body}:J{$detail_body}")
                            ->applyFromArray([
                                'alignment' => [
                                    'horizontal' =>
                                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                                ],
                            ]);
                    }
                }

                $sheet
                    ->getStyle("A{$detail_head2}:K{$detail_body}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' =>
                                    \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                $prg_row = $detail_body + 2;
            }
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $t = time();
        $filename = "Laporan-Capaian-Kinerja-Bulanan-{$t}.{$ext}";
        $filepath = "download/{$filename}";
        $writer->save(DOC_ROOT . $filepath);
        echo json_encode($filepath);
    }
}

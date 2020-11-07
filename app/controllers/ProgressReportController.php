<?php
use app\controllers\Controller;
use app\models\ProgramModel;
use app\models\ActivityModel;
use app\models\ProgressReportModel;
use app\models\PackageDetailModel;
use app\models\PackageModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProgressReportController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setControllerAttribute(__CLASS__);
        $this->title = 'Pekembangan Capaian Kinerja';
        $this->smarty->assign('title', $this->title);
        $this->ProgressReportModel = new ProgressReportModel();

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
            ['act_name', 'ASC'],
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
        $list = $this->ProgressReportModel->getData($_POST);

        echo json_encode($list);
        exit();
    }

    public function downloadSpreadsheet()
    {
        $ext = $_POST['ext'] ?? 'xls';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $list = $this->ProgressReportModel->getData($_POST);
        $list_count = count($list);

        $lastCol = 'N';

        $titles = [
            'LAPORAN PERKEMBANGAN CAPAIAN KINERJA',
            'BINA MARGA KAB. SEMARANG',
            "THN ANGGARAN: {$_POST['pkg_fiscal_year']}",
        ];

        for ($i = 1; $i <= 3; $i++) {
            $sheet->setCellValue("A{$i}", $titles[$i - 1]);
            $sheet->mergeCells("A{$i}:{$lastCol}{$i}");
        }

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

        $alphabet = range('A', $lastCol);
        if (($key = array_search('C', $alphabet)) !== false) {
            unset($alphabet[$key]);
        }
        $alphabet = array_values($alphabet);

        if ($list_count > 0) {
            foreach ($list as $rows) {
                $prg_row = $prg_row ?? 5;
                $act_row = $prg_row + 1;

                $sheet->mergeCells("A{$prg_row}:B{$prg_row}");
                $sheet->setCellValue("A{$prg_row}", 'Program:');
                $sheet->setCellValue("C{$prg_row}", $rows['prg_name']);

                $sheet->mergeCells("A{$act_row}:B{$act_row}");
                $sheet->setCellValue("A{$act_row}", 'Kegiatan:');
                $sheet->setCellValue("C{$act_row}", $rows['act_name']);

                $detail_head1 = $act_row + 1;
                $detail_head2 = $detail_head1 + 1;

                $sheet->mergeCells("A{$detail_head1}:A{$detail_head2}");
                $sheet->mergeCells("B{$detail_head1}:C{$detail_head2}");

                $cols1 = range('D', 'H');
                foreach ($cols1 as $col) {
                    $sheet->mergeCells(
                        "{$col}{$detail_head1}:{$col}{$detail_head2}",
                    );
                }

                $cols2 = range('I', 'M', 2);
                $cols3 = range('J', 'N', 2);
                for ($i = 0; $i < 3; $i++) {
                    $sheet->mergeCells(
                        "{$cols2[$i]}{$detail_head1}:{$cols3[$i]}{$detail_head1}",
                    );
                }

                // $sheet->getRowDimension($detail_head1)->setRowHeight(30);
                $sheet->getRowDimension($detail_head2)->setRowHeight(30);

                $sheet->fromArray(
                    [
                        'No.',
                        'Paket Kegiatan',
                        '',
                        "Nilai Awal Kontrak\n(Rp)",
                        "Nilai Kontrak Akhir\n(Rp)",
                        "Pagu Anggaran Fisik\n(Rp)",
                        "Minggu\nKe",
                        "Tanggal\nPeriode",
                        'Target',
                        '',
                        'Realisasi',
                        '',
                        'Deviasi',
                        '',
                    ],
                    null,
                    "A{$detail_head1}",
                );
                $sheet->fromArray(
                    [
                        "Fisik\n(%)",
                        "Keuangan\n(%)",
                        "Fisik\n(%)",
                        "Keuangan\n(%)",
                        "Fisik\n(%)",
                        "Keuangan\n(%)",
                    ],
                    null,
                    "I{$detail_head2}",
                );

                $sheet
                    ->getStyle("A{$detail_head1}:{$lastCol}{$detail_head2}")
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
                    foreach ($pkg as $row) {
                        $detail_body = $detail_head2 + $n;
                        $n++;

                        $sheet->mergeCells("B{$detail_body}:C{$detail_body}");

                        $number = '';
                        if ($row['week'] <= 1) {
                            $number = $no;
                            $no++;
                        }

                        $content = [
                            $number,
                            $row['pkgd_name'],
                            $row['cnt_value'],
                            $row['cnt_value_end'],
                            $row['pkgd_debt_ceiling'],
                            $row['week'],
                            $row['trg_date'],
                            $row['trg_physical'],
                            $row['trg_finance_pct'],
                            $row['prog_physical'],
                            $row['prog_finance_pct'],
                            $row['devn_physical'],
                            $row['devn_finance_pct'],
                        ];

                        foreach ($alphabet as $key => $value) {
                            $sheet->setCellValue(
                                "{$value}{$detail_body}",
                                $content[$key],
                            );
                        }

                        $sheet
                            ->getStyle("D{$detail_body}:E{$detail_body}")
                            ->applyFromArray([
                                'alignment' => [
                                    'horizontal' =>
                                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                                ],
                            ]);

                        $sheet
                            ->getStyle("F{$detail_body}:H{$detail_body}")
                            ->applyFromArray([
                                'alignment' => [
                                    'horizontal' =>
                                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                ],
                            ]);

                        $sheet
                            ->getStyle("I{$detail_body}:N{$detail_body}")
                            ->applyFromArray([
                                'alignment' => [
                                    'horizontal' =>
                                        \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                                ],
                            ]);
                    }
                }

                $sheet
                    ->getStyle("A{$detail_head2}:{$lastCol}{$detail_body}")
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

        foreach ($alphabet as $value) {
            $sheet->getColumnDimension($value)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $t = time();
        $filename = "Laporan-Perekembangan-Capaian-Kinerja-{$t}.{$ext}";
        $filepath = "download/{$filename}";
        $writer->save(DOC_ROOT . $filepath);
        echo json_encode($filepath);
    }
}

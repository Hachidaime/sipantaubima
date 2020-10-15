<?php

use app\controllers\Controller;
use app\helper\File;
use app\helper\Flasher;
use app\helper\Functions;
use app\models\ProgressModel;
use app\models\PackageDetailModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * @desc this class will handle Uang controller
 *
 * @class BankController
 * @extends Controller
 * @author Hachidaime
 */

class ProgressController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setControllerAttribute(__CLASS__);
        $this->title = 'Progres Paket';
        $this->smarty->assign('title', $this->title);

        $this->progressModel = new ProgressModel();
        $this->packageDetailModel = new PackageDetailModel();

        if (!$_SESSION['USER']['usr_is_progress']) {
            header('Location:' . BASE_URL . '/403');
        }
    }

    public function index()
    {
        $this->smarty->assign('breadcrumb', [
            ['Paket Pekerjaan', ''],
            [$this->title, ''],
        ]);

        $this->smarty->assign('subtitle', "Daftar {$this->title}");

        $this->smarty->display("{$this->directory}/index.tpl");
    }

    public function search()
    {
        list($list, $info) = $this->getList(true);

        echo json_encode([
            'list' => $list,
            'info' => $info,
        ]);
        exit();
    }

    /**
     * @desc this method will handle Data Uang form
     *
     * @method form
     * @param int $id is mata uang id
     */
    public function form(int $id = null)
    {
        $tag = 'Tambah';
        if (!is_null($id)) {
            list(, $count) = $this->progressModel->singlearray($id);
            if (!$count) {
                Flasher::setFlash(
                    'Data tidak ditemukan!',
                    $this->name,
                    'error',
                );
                header('Location: ' . BASE_URL . "/{$this->lowerName}");
            }

            $tag = 'Ubah';
            $this->smarty->assign('id', $id);
        }

        $this->smarty->assign('breadcrumb', [
            ['Paket Pekerjaan', ''],
            [$this->title, $this->lowerName],
            [$tag, ''],
        ]);

        list($packageDetail) = $this->packageDetailModel->multiarray(
            [['pkg_id >', 0]],
            [['pkgd_no', 'ASC']],
        );

        $this->smarty->assign('subtitle', "{$tag} {$this->title}");
        $this->smarty->assign('package_detail', $packageDetail);

        $this->smarty->display("{$this->directory}/form.tpl");
    }

    public function detail()
    {
        list($detail) = $this->progressModel->singlearray($_POST['id']);
        $detail['prog_date'] = Functions::dateFormat(
            'Y-m-d',
            'd/m/Y',
            $detail['prog_date'],
        );

        echo json_encode($detail);
        exit();
    }

    public function submit()
    {
        $data = $_POST;
        $data['prog_date'] = !empty($data['prog_date'])
            ? Functions::dateFormat('d/m/Y', 'Y-m-d', $data['prog_date'])
            : null;
        $data['prog_finance'] = !empty($data['prog_finance'])
            ? str_replace(',', '.', $data['prog_finance'])
            : 0;
        if ($this->validate($data)) {
            $result = $this->progressModel->save($data);
            if ($data['id'] > 0) {
                $tag = 'Ubah';
                $id = $data['id'];
            } else {
                $tag = 'Tambah';
                $id = $result;
            }

            if ($result) {
                $update = ['id' => $id];
                if (!empty($data['prog_img'])) {
                    $imgdir = "img/progress/{$id}";
                    $prog_img = File::moveFromTemp(
                        $imgdir,
                        $data['prog_img'],
                        false,
                        true,
                    );
                    $update['prog_img'] = $prog_img
                        ? $prog_img
                        : $data['prog_img'];
                }

                if (!empty($data['prog_doc'])) {
                    $docdir = "pdf/progress/{$id}";
                    $prog_doc = File::moveFromTemp(
                        $docdir,
                        $data['prog_doc'],
                        false,
                        true,
                    );
                    $update['prog_doc'] = $prog_doc
                        ? $prog_doc
                        : $data['prog_doc'];
                }

                $this->progressModel->save($update);

                $this->updatePackageDetail($data['pkgd_id']);

                Flasher::setFlash(
                    "Berhasil {$tag} {$this->title}.",
                    $this->name,
                    'success',
                );

                list($packageDetail) = $this->packageDetailModel->singlearray(
                    $data['pkgd_id'],
                );

                $this->writeLog(
                    "{$tag} {$this->title}",
                    "{$tag} {$this->title} [{$packageDetail['pkgd_no']} - {$packageDetail['pkgd_name']}] berhasil.",
                );
                echo json_encode(['success' => true]);
            } else {
                echo json_encode([
                    'success' => false,
                    'msg' => "Gagal {$tag} {$this->title}.",
                ]);
            }
            exit();
        }
    }

    public function validate($data)
    {
        $validation = $this->validator->make($data, [
            'prog_fiscal_year' => 'required',
            'prog_week' => "required|min:1|uniq_prog:{$data['pkgd_id']},{$data['id']}",
            'prog_date' => 'required|date',
            'pkgd_id' => 'required',
            'prog_physical' => 'required|numeric',
            'prog_img' => 'required',
        ]);

        $validation->setAliases([
            'prog_fiscal_year' => 'Tahun Anggaran',
            'prog_week' => 'Minggu Ke-',
            'prog_date' => 'Tanggal Progres',
            'pkgd_id' => 'Nama Paket',
            'prog_physical' => 'Progres Fisik',
            'prog_img' => 'Foto',
        ]);

        $validation->setMessages([
            'required' => '<strong>:attribute</strong> harus diisi.',
            'unique' => '<strong>:attribute</strong> sudah ada di database.',
            'date' => 'Format <strong>:attribute</strong> tidak valid.',
            'numeric' => '<strong>:attribute</strong> tidak valid.',
            'prog_week:uniq_prog' =>
                '<strong>:attribute:value</strong> telah ada di database.',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            echo json_encode([
                'success' => false,
                'msg' => $validation->errors()->firstOfAll(),
            ]);
            exit();
        }
        return true;
    }

    public function remove()
    {
        $id = (int) $_POST['id'];
        $tag = 'Hapus';
        list($data) = $this->progressModel->singlearray($id);
        $result = $this->progressModel->delete($id);

        if ($result) {
            Flasher::setFlash(
                "Berhasil {$tag} {$this->title}.",
                $this->name,
                'success',
            );

            list($packageDetail) = $this->packageDetailModel->singlearray(
                $data['pkgd_id'],
            );
            $this->writeLog(
                "{$tag} {$this->title}",
                "{$tag} {$this->title} [{$packageDetail['pkgd_no']} - {$packageDetail['pkgd_name']}] berhasil.",
            );
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false,
                'msg' => "Gagal {$tag} {$this->title}.",
            ]);
        }
        exit();
    }

    private function updatePackageDetail($pkgd_id)
    {
        $query = "SELECT
            MAX(prog_week) as pkgd_last_prog_week,
            SUM(prog_finance) as pkgd_sum_prog_finance, 
            MAX(prog_physical) as pkgd_sum_prog_physical,
            MAX(prog_date) as pkgd_last_prog_date,
            (SELECT prog_img FROM {$this->progressModel->getTable()} 
                WHERE pkgd_id = ? 
                ORDER BY id DESC 
                LIMIT 1) as pkgd_last_prog_img,
            MAX(id) as prog_id,
            pkgd_id as id
            FROM {$this->progressModel->getTable()} 
            WHERE pkgd_id = ?";
        $data = $this->progressModel->db
            ->query($query, [$pkgd_id, $pkgd_id])
            ->first()
            ->toArray();
        $data[
            'pkgd_last_prog_img'
        ] = "img/progress/{$data['prog_id']}/{$data['pkgd_last_prog_img']}";
        unset($data['prog_id']);

        $this->packageDetailModel->save($data);
        // var_dump($this->packageDetailModel->db);
    }

    public function downloadSpreadsheet()
    {
        $ext = $_POST['ext'] ?? 'xls';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        list($list, $list_count) = $this->getList();

        $spreadsheet
            ->getActiveSheet()
            ->fromArray(
                [
                    'No.',
                    "Tahun\nAnggaran",
                    'Minggu Ke',
                    'Nama Paket',
                    'Tanggal Periode',
                    'Progres Fisik',
                    'Progres Keuangan',
                ],
                null,
                'A1',
            );

        $spreadsheet
            ->getActiveSheet()
            ->getRowDimension('1')
            ->setRowHeight(30);

        $spreadsheet
            ->getActiveSheet()
            ->getStyle('A1:G1')
            ->applyFromArray([
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

        if ($list_count > 0) {
            foreach ($list as $idx => $rows) {
                $row = $idx + 2;
                $n = $idx + 1;
                $sheet->setCellValue("A{$row}", $n);
                $sheet->setCellValue("B{$row}", $rows['prog_fiscal_year']);
                $sheet->setCellValue("C{$row}", $rows['prog_week']);
                $sheet->setCellValue("D{$row}", $rows['pkgd_name']);
                $sheet->setCellValue("E{$row}", $rows['prog_date']);
                $sheet->setCellValue("F{$row}", $rows['prog_physical']);
                $sheet->setCellValue("G{$row}", $rows['prog_finance']);
            }

            $spreadsheet
                ->getActiveSheet()
                ->getStyle("A2:G{$row}")
                ->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' =>
                                \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
        }

        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('A')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('B')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('C')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('D')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('E')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('F')
            ->setAutoSize(true);
        $spreadsheet
            ->getActiveSheet()
            ->getColumnDimension('G')
            ->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $t = time();
        $filename = "Progres-{$t}.{$ext}";
        $filepath = "download/{$filename}";
        $writer->save(DOC_ROOT . $filepath);
        echo json_encode($filepath);
    }

    private function getList($paginate = false)
    {
        $page = $_POST['page'] ?? 1;
        $keyword = $_POST['keyword'] ?? null;

        list($packageDetail) = $this->packageDetailModel->multiarray();

        $packageDetailOptions = Functions::listToOptions(
            $packageDetail,
            'id',
            'pkgd_name',
        );

        $filter =
            !is_null($keyword) && !empty($keyword) && $keyword != ''
                ? [['pkg_fiscal_year', 'LIKE', "%{$keyword}%"]]
                : null;

        $sort = [['prog_fiscal_year', 'ASC'], ['id', 'DESC']];

        list($list, $info) = $paginate
            ? $this->progressModel->paginate($page, $filter, $sort)
            : $this->progressModel->multiarray($filter, $sort);

        foreach ($list as $idx => $row) {
            $list[$idx]['prog_date'] = Functions::dateFormat(
                'Y-m-d',
                'd/m/Y',
                $row['prog_date'],
            );
            $list[$idx]['pkgd_name'] = $packageDetailOptions[$row['pkgd_id']];
            $list[$idx]['prog_finance'] = number_format(
                $row['prog_finance'],
                2,
                ',',
                '.',
            );
        }

        return [$list, $info];
    }
}

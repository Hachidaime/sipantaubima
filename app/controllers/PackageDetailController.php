<?php

use app\controllers\Controller;
use app\helper\Functions;
use app\models\LocationModel;
use app\models\PackageDetailModel;
use app\models\PackageModel;
use app\models\ProgressModel;
use app\models\TargetModel;

class PackageDetailController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setControllerAttribute(__CLASS__);
        $this->title = 'Detail Pemaketan';

        $this->packageDetailModel = new PackageDetailModel();

        if (!$_SESSION['USER']['usr_is_package']) {
            header('Location:' . BASE_URL . '/403');
        }
    }

    public function search()
    {
        $packageModel = new PackageModel();
        list($detail, $dcount) = $packageModel->singlearray($_POST['pkg_id']);

        $locationModel = new LocationModel();
        list($location) = $locationModel->multiarray(null, [
            ['loc_code', 'ASC']
        ]);
        $location_opt = Functions::listToOptions($location, 'id', 'loc_name');

        list($list, $lcount) = $this->packageDetailModel->multiarray([
            ['pkg_id', $detail['id'] > 0 ? $detail['id'] : 0],
            [
                'pkgs_id',
                $detail['pkgs_id'] > 0
                    ? $detail['pkgs_id']
                    : $_SESSION['PKGS_ID']
            ]
        ]);

        $pkg_debt_ceiling = 0;
        foreach ($list as $idx => $row) {
            $pkg_debt_ceiling += $row['pkgd_debt_ceiling'];

            $row = array_merge($row, [
                'pkgd_debt_ceiling' => number_format(
                    $row['pkgd_debt_ceiling'],
                    2,
                    ',',
                    '.'
                ),
                'pkgd_sof_name' => SOF_OPT[$row['pkgd_sof']],
                'pkgd_loc_name' => $location_opt[$row['pkgd_loc_id']],
                'pkgd_last_prog_date' => !is_null($row['pkgd_last_prog_date'])
                    ? Functions::dateFormat(
                        'Y-m-d',
                        'd/m/Y',
                        $row['pkgd_last_prog_date']
                    )
                    : null,
                'pkgd_sum_prog_physical' => number_format(
                    $row['pkgd_sum_prog_physical'],
                    2,
                    ',',
                    '.'
                ),
                'pkgd_sum_prog_finance' => number_format(
                    $row['pkgd_sum_prog_finance'],
                    2,
                    ',',
                    '.'
                ),
                'pkgd_pho_date' => !is_null($row['pkgd_pho_date'])
                    ? Functions::dateFormat(
                        'Y-m-d',
                        'd/m/Y',
                        $row['pkgd_pho_date']
                    )
                    : null
            ]);

            $list[$idx] = $row;
        }

        $pkg_debt_ceiling = number_format($pkg_debt_ceiling, 2, ',', '.');

        $result = ['list' => $list, 'pkg_debt_ceiling' => $pkg_debt_ceiling];
        echo json_encode($result);
        exit();
    }

    public function detail()
    {
        list($detail) = $this->packageDetailModel->singlearray($_POST['id']);
        echo json_encode($detail);
        exit();
    }

    public function submit()
    {
        $data = $_POST;
        $data['pkgd_no'] = strtoupper($data['pkgd_no']);
        $data['pkgs_id'] = $_SESSION['PKGS_ID'];
        $data['pkg_id'] = !empty($data['pkg_id']) ? $data['pkg_id'] : 0;
        $data['pkgd_debt_ceiling'] =
            $data['pkgd_debt_ceiling'] > 0 ? $data['pkgd_debt_ceiling'] : '';
        if ($this->validate($data)) {
            $data['pkgd_debt_ceiling'] = !empty($data['pkgd_debt_ceiling'])
                ? str_replace(',', '.', $data['pkgd_debt_ceiling'])
                : 0;
            $result = $this->packageDetailModel->save($data);

            if ($data['id'] > 0) {
                $tag = 'Ubah';
                $id = $data['id'];
            } else {
                $tag = 'Tambah';
                $id = $result;
            }

            if ($result) {
                $this->writeLog(
                    "{$tag} {$this->title}",
                    "{$tag} {$this->title} [{$id}] berhasil."
                );
                echo json_encode([
                    'success' => true,
                    'msg' => "Berhasil {$tag} {$this->title}."
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'msg' => "Gagal {$tag} {$this->title}."
                ]);
            }
            exit();
        }
    }

    public function validate($data)
    {
        $validation = $this->validator->make($data, [
            'pkgd_no' => "required|uniq_pkgd_no:{$data['pkgs_id']},{$data['id']}",
            'pkgd_name' => 'required',
            'pkgd_sof' => 'required',
            'pkgd_debt_ceiling' => 'required',
            'pkgd_loc_id' => 'required'
        ]);

        $validation->setAliases([
            'pkgd_no' => 'Nomor Paket',
            'pkgd_name' => 'Nama Paket',
            'pkgd_sof' => 'Sumber Dana',
            'pkgd_debt_ceiling' => 'Pagu Anggaran',
            'pkgd_loc_id' => 'Lokasi Pekerjaan'
        ]);

        $validation->setMessages([
            'required' => '<strong>:attribute</strong> harus diisi.',
            'pkgd_no:uniq_pkgd_no' => 'Data sudah ada di database.'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            echo json_encode([
                'success' => false,
                'msg' => $validation->errors()->firstOfAll()
            ]);
            exit();
        }
        return true;
    }

    public function remove()
    {
        $id = (int) $_POST['id'];
        $tag = 'Hapus';
        $result = $this->packageDetailModel->delete($id);

        if ($result) {
            $this->removeTarget($id);
            $this->removeProgress($id);

            $this->writeLog(
                "{$tag} {$this->title}",
                "{$tag} {$this->title} [{$id}] berhasil."
            );
            echo json_encode([
                'success' => true,
                'msg' => "Berhasil {$tag} {$this->title}."
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'msg' => "Gagal {$tag} {$this->title}."
            ]);
        }
        exit();
    }

    private function removeTarget(int $pkdg_id)
    {
        $targetModel = new TargetModel();
        $targetModel->delete([['pkgd_id', $pkdg_id]]);
    }

    private function removeProgress(int $pkgd_id)
    {
        $progressModel = new ProgressModel();
        list($list, $count) = $progressModel->multiarray([
            ['pkgd_id', $pkgd_id]
        ]);

        if ($count > 0) {
            foreach ($list as $row) {
                $imgdir = DOC_ROOT . "upload/img/progress/{$row['id']}";
                array_map('unlink', glob("{$imgdir}/{$row['prog_img']}"));
                rmdir($imgdir);

                $pdfdir = DOC_ROOT . "upload/pdf/progress/{$row['id']}";
                array_map('unlink', glob("{$pdfdir}/{$row['prog_doc']}"));
                rmdir($pdfdir);
            }
        }

        $progressModel->delete([['pkgd_id', $pkgd_id]]);
    }

    public function submitExpires()
    {
        $data = $_POST;
        $data['pkgd_pho_date'] = !empty($data['pkgd_pho_date'])
            ? Functions::dateFormat('d/m/Y', 'Y-m-d', $data['pkgd_pho_date'])
            : null;
        $data['pkgd_contract_fv'] =
            $data['pkgd_contract_fv'] > 0 ? $data['pkgd_contract_fv'] : '';

        if ($this->validateExpires($data)) {
            $data['pkgd_contract_fv'] = !empty($data['pkgd_contract_fv'])
                ? str_replace(',', '.', $data['pkgd_contract_fv'])
                : 0;

            $result = $this->packageDetailModel->save($data);
            $id = $data['id'];
            $tag = 'Ubah';

            if ($result) {
                $this->writeLog(
                    "{$tag} {$this->title}",
                    "{$tag} {$this->title} [{$id}], Kontrak Berakhir berhasil."
                );
                echo json_encode([
                    'success' => true,
                    'msg' => 'Berhasil Kontrak Berakhir.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'msg' => 'Gagal Kontrak Berakhir.'
                ]);
            }
            exit();
        }
    }

    private function validateExpires($data)
    {
        $validation = $this->validator->make($data, [
            'pkgd_pho_date' => 'required|date',
            'pkgd_contract_fv' => 'required'
        ]);

        $validation->setAliases([
            'pkgd_pho_date' => 'Tanggal PHO',
            'pkgd_contract_fv' => 'Nilai Akhir Kontrak'
        ]);

        $validation->setMessages([
            'required' => '<strong>:attribute</strong> harus diisi.',
            'date' => 'Format <strong>:attribute</strong> tidak valid.'
        ]);

        $validation->validate();

        $errors = [];
        if ($validation->fails()) {
            $errors = $validation->errors()->firstOfAll();
        }

        $progressModel = new ProgressModel();
        list($progress, $progressCount) = $progressModel->multiarray([
            ['pkgd_id', $data['id']]
        ]);

        if ($progressCount === 0) {
            $errors['progress'] = 'Paket ini belum ada progress.';
        }

        if ($errors) {
            echo json_encode([
                'success' => false,
                'msg' => $errors
            ]);
            exit();
        }

        return true;
    }
}

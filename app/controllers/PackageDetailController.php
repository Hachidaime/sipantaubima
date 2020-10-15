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
            ['loc_code', 'ASC'],
        ]);
        $location_opt = Functions::listToOptions($location, 'id', 'loc_name');

        list($list, $lcount) = $this->packageDetailModel->multiarray([
            ['pkg_id', $detail['id'] > 0 ? $detail['id'] : 0],
            [
                'pkgs_id',
                $detail['pkgs_id'] > 0
                    ? $detail['pkgs_id']
                    : $_SESSION['PKGS_ID'],
            ],
        ]);

        foreach ($list as $idx => $row) {
            $row['pkgd_sof_name'] = SOF_OPT[$row['pkgd_sof']];
            $row['pkgd_loc_name'] = $location_opt[$row['pkgd_loc_id']];
            $row['pkgd_contract_date'] = !is_null($row['pkgd_contract_date'])
                ? Functions::dateFormat(
                    'Y-m-d',
                    'd/m/Y',
                    $row['pkgd_contract_date'],
                )
                : null;

            $row['pkgd_contract_end_date'] = !is_null(
                $row['pkgd_contract_end_date'],
            )
                ? Functions::dateFormat(
                    'Y-m-d',
                    'd/m/Y',
                    $row['pkgd_contract_end_date'],
                )
                : null;

            $row['pkgd_addendum_date'] = !is_null($row['pkgd_addendum_date'])
                ? Functions::dateFormat(
                    'Y-m-d',
                    'd/m/Y',
                    $row['pkgd_addendum_date'],
                )
                : null;

            $row['pkgd_addendum_end_date'] = !is_null(
                $row['pkgd_addendum_end_date'],
            )
                ? Functions::dateFormat(
                    'Y-m-d',
                    'd/m/Y',
                    $row['pkgd_addendum_end_date'],
                )
                : null;

            $row['pkgd_last_prog_date'] = !is_null($row['pkgd_last_prog_date'])
                ? Functions::dateFormat(
                    'Y-m-d',
                    'd/m/Y',
                    $row['pkgd_last_prog_date'],
                )
                : null;

            $row['pkgd_sum_prog_physical'] = number_format(
                $row['pkgd_sum_prog_physical'],
                2,
                ',',
                '.',
            );

            $row['pkgd_sum_prog_finance'] = number_format(
                $row['pkgd_sum_prog_finance'],
                2,
                ',',
                '.',
            );

            $list[$idx] = $row;
        }

        echo json_encode($list);
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
        if ($this->validate($data)) {
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
                    "{$tag} {$this->title} [{$id}] berhasil.",
                );
                echo json_encode([
                    'success' => true,
                    'msg' => "Berhasil {$tag} {$this->title}.",
                ]);
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
            'pkgd_no' => "required|uniq_pkgd_no:{$data['pkgs_id']},{$data['id']}",
            'pkgd_name' => 'required',
            'pkgd_sof' => 'required',
            'pkgd_loc_id' => 'required',
        ]);

        $validation->setAliases([
            'pkgd_no' => 'Nomor Paket',
            'pkgd_name' => 'Nama Paket',
            'pkgd_sof' => 'Sumber Dana',
            'pkgd_loc_id' => 'Lokasi Pekerjaan',
        ]);

        $validation->setMessages([
            'required' => '<strong>:attribute</strong> harus diisi.',
            'pkgd_no:uniq_pkgd_no' => 'Data sudah ada di database.',
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
        $result = $this->packageDetailModel->delete($id);

        if ($result) {
            $this->removeTarget($id);
            $this->removeProgress($id);

            $this->writeLog(
                "{$tag} {$this->title}",
                "{$tag} {$this->title} [{$id}] berhasil.",
            );
            echo json_encode([
                'success' => true,
                'msg' => "Berhasil {$tag} {$this->title}.",
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'msg' => "Gagal {$tag} {$this->title}.",
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
            ['pkgd_id', $pkgd_id],
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
}

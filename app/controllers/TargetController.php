<?php

use app\controllers\Controller;
use app\models\TargetModel;
use app\helper\Functions;
use app\models\PackageDetailModel;

class TargetController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setControllerAttribute(__CLASS__);

        $this->targetModel = new TargetModel();
        $this->packageDetailModel = new PackageDetailModel();

        if (!$_SESSION['USER']['usr_is_package']) {
            header('Location:' . BASE_URL . '/403');
        }
    }

    public function search()
    {
        list($list, $lcount) = $this->targetModel->multiarray([
            ['pkgd_id', $_POST['pkgd_id']],
        ]);

        foreach ($list as $idx => $row) {
            $row['trg_date'] = Functions::dateFormat(
                'Y-m-d',
                'd/m/Y',
                $row['trg_date'],
            );
            $row['trg_finance'] = number_format(
                $row['trg_finance'],
                2,
                ',',
                '.',
            );

            $list[$idx] = $row;
        }

        echo json_encode($list);
        exit();
    }

    public function submit()
    {
        $data = $_POST;
        $data['trg_date'] = !empty($data['trg_date'])
            ? Functions::dateFormat('d/m/Y', 'Y-m-d', $data['trg_date'])
            : null;
        $data['trg_finance'] = !empty($data['trg_finance'])
            ? str_replace(',', '.', $data['trg_finance'])
            : null;
        if ($this->validate($data)) {
            $result = $this->targetModel->save($data);
            if ($data['id'] > 0) {
                $tag = 'Ubah';
                $id = $data['id'];
            } else {
                $tag = 'Tambah';
                $id = $result;
            }

            if ($result) {
                list($packageDetail) = $this->packageDetailModel->singlearray(
                    $data['pkgd_id'],
                );
                $this->writeLog(
                    "{$tag} {$this->title}",
                    "{$tag} {$this->title} [{$packageDetail['pkgd_no']} - {$packageDetail['pkgd_name']}] berhasil.",
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
            'trg_week' => "required|min:1|uniq_trg:{$data['pkgd_id']},{$data['id']}",
            'trg_date' => 'required|date',
            'trg_physical' => 'required|numeric',
            'trg_finance' => 'required',
        ]);

        $validation->setAliases([
            'trg_week' => 'Minggu Ke-',
            'trg_date' => 'Tanggal Periode',
            'trg_physical' => 'Target Fisik',
            'trg_finance' => 'Target Keuangan',
        ]);

        $validation->setMessages([
            'required' => '<strong>:attribute</strong> harus diisi.',
            'numeric' => '<strong>:attribute</strong> tidak valid.',
            'date' => 'Format <strong>:attribute</strong> tidak valid.',
            'trg_week:min' => '<strong>:attribute</strong> minimum :min.',
            'trg_week:uniq_trg' =>
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
        list($data) = $this->targetModel->singlearray($id);
        $result = $this->targetModel->delete($id);

        if ($result) {
            list($packageDetail) = $this->packageDetailModel->singlearray(
                $data['pkgd_id'],
            );
            $this->writeLog(
                "{$tag} {$this->title}",
                "{$tag} {$this->title} [{$packageDetail['pkgd_no']} - {$packageDetail['pkgd_name']}] berhasil.",
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

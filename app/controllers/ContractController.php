<?php

use app\controllers\Controller;
use app\helper\Functions;
use app\models\PackageDetailModel;
use app\models\ContractModel;
use app\models\AddendumModel;
use app\models\UserModel;

class ContractController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setControllerAttribute(__CLASS__);
        $this->title = 'Kontaktor';

        $this->packageDetailModel = new PackageDetailModel();
        $this->contractModel = new ContractModel();
        $this->addendumModel = new AddendumModel();
        $this->UserModel = new UserModel();

        if (!$_SESSION['USER']['usr_is_package']) {
            header('Location:' . BASE_URL . '/403');
        }
    }

    public function detail()
    {
        $pkgd_id = $_POST['pkgd_id'];
        list($detail) = $this->contractModel->singlearray([
            ['pkgd_id', $pkgd_id]
        ]);

        $detail['cnt_date'] = !is_null($detail['cnt_date'])
            ? Functions::dateFormat('Y-m-d', 'd/m/Y', $detail['cnt_date'])
            : '';

        $detail['cnt_wsw_date'] = !is_null($detail['cnt_wsw_date'])
            ? Functions::dateFormat('Y-m-d', 'd/m/Y', $detail['cnt_wsw_date'])
            : '';

        $detail['cnt_plan_pho_date'] = !is_null($detail['cnt_plan_pho_date'])
            ? Functions::dateFormat(
                'Y-m-d',
                'd/m/Y',
                $detail['cnt_plan_pho_date']
            )
            : '';

        list($addendum, $addendum_c) = $this->addendumModel->multiarray([
            ['pkgd_id', $pkgd_id]
        ]);

        $detail['addendum'] = [];
        if ($addendum_c > 0) {
            foreach ($addendum as $idx => $row) {
                $row['add_date'] = !is_null($row['add_date'])
                    ? Functions::dateFormat('Y-m-d', 'd/m/Y', $row['add_date'])
                    : '';

                $row['add_plan_pho_date'] = !is_null($row['add_plan_pho_date'])
                    ? Functions::dateFormat(
                        'Y-m-d',
                        'd/m/Y',
                        $row['add_plan_pho_date']
                    )
                    : '';

                $row['add_days'] = $row['add_days'] > 0 ? $row['add_days'] : '';

                $row['add_value'] =
                    $row['add_value'] > 0
                        ? number_format($row['add_value'], 2, ',', '.')
                        : '';

                $addendum[$idx] = $row;
            }
            $detail['addendum'] = $addendum;
        }

        echo json_encode($detail);
        exit();
    }

    public function submit()
    {
        $data = $_POST;

        $data['cnt_no'] = strtoupper($data['cnt_no']);
        $data['cnt_date'] = !empty($data['cnt_date'])
            ? Functions::dateFormat('d/m/Y', 'Y-m-d', $data['cnt_date'])
            : null;
        $data['cnt_wsw_date'] = !empty($data['cnt_wsw_date'])
            ? Functions::dateFormat('d/m/Y', 'Y-m-d', $data['cnt_wsw_date'])
            : null;
        $data['cnt_plan_pho_date'] = !empty($data['cnt_plan_pho_date'])
            ? Functions::dateFormat(
                'd/m/Y',
                'Y-m-d',
                $data['cnt_plan_pho_date']
            )
            : null;
        $data['cnt_value'] = !empty($data['cnt_value'])
            ? str_replace(',', '.', $data['cnt_value'])
            : 0;

        list($user) = $this->UserModel->singlearray([['id', $data['usr_id']]]);
        $data['cnt_contractor_name'] = $user['usr_consultant_name'];

        $addendum = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $val) {
                    $addendum[$k][$key] = $val;
                }
                unset($data[$key]);
            } else {
                if (empty($value)) {
                    unset($data[$key]);
                }
            }
        }

        if ($this->validate($data, $addendum)) {
            $result = $this->contractModel->save($data);

            $tag = 'Ubah';
            $id = $data['id'];

            if ($result) {
                $this->submitAddendum($addendum);
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

    public function validate($data, $addendum)
    {
        $validation = $this->validator->make($data, [
            'cnt_contractor_name' => 'required|max:250',
            'cnt_no' => 'required|max:50',
            'cnt_date' => 'required|date',
            'cnt_wsw_date' => 'required|date',
            'cnt_days' => 'required',
            'cnt_plan_pho_date' => 'required|date',
            'cnt_value' => 'required',
            'usr_id' => 'required'
        ]);

        $validation->setAliases([
            'cnt_contractor_name' => 'Nama Kontraktor',
            'cnt_no' => 'Nomor Kontrak',
            'cnt_date' => 'Tanggal Kontrak',
            'cnt_wsw_date' => 'Tanggal SPMK',
            'cnt_days' => 'Waktu Pelaksanaan',
            'cnt_plan_pho_date' => 'Tanggal Rencana PHO',
            'cnt_value' => 'Nilai Kontrak',
            'usr_id' => 'Nama Konsultan'
        ]);

        $validation->setMessages([
            'required' => '<strong>:attribute</strong> harus diisi.',
            'max' => '<strong>:attribute</strong> maximum :max karakter.',
            'date' => 'Format <strong>:attribute</strong> tidak valid.'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            echo json_encode([
                'success' => false,
                'msg' => $validation->errors()->firstOfAll()
            ]);
            exit();
        } else {
            $this->validateAddendum($data, $addendum);
        }
        return true;
    }

    private function submitAddendum($data)
    {
        foreach ($data as $row) {
            $addendum = [
                'pkgd_id' => $_POST['pkgd_id'],
                'id' => $row['add_id'],
                'add_no' => strtoupper($row['add_no']),
                'add_order' => $row['add_order'],
                'add_date' => !empty($row['add_date'])
                    ? Functions::dateFormat('d/m/Y', 'Y-m-d', $row['add_date'])
                    : '',
                'add_days' => $row['add_days'],
                'add_plan_pho_date' => !empty($row['add_plan_pho_date'])
                    ? Functions::dateFormat(
                        'd/m/Y',
                        'Y-m-d',
                        $row['add_plan_pho_date']
                    )
                    : '',
                'add_value' => !empty($row['add_value'])
                    ? str_replace(',', '.', $row['add_value'])
                    : ''
            ];

            foreach ($addendum as $key => $value) {
                if (empty($value)) {
                    unset($addendum[$key]);
                }
            }

            $this->addendumModel->save($addendum);
        }
    }

    private function validateAddendum($data, $addendum)
    {
        $errors = [];
        foreach ($addendum as $idx => $row) {
            foreach ($row as $key => $value) {
                $value =
                    $key == 'add_value'
                        ? str_replace(',', '.', $value)
                        : $value;
                $row[$key . $idx] = $value;
                unset($row[$key]);
            }

            if ($row["add_value{$idx}"] > $data['cnt_value']) {
                $errors["add_value{$idx}"] =
                    '<strong>Nilai Addendum</strong> tidak boleh lebih dari <strong>Nilai Kontrak</strong>.';
            }
        }

        if ($errors) {
            echo json_encode([
                'success' => false,
                'msg' => $errors
            ]);
            exit();
        }
    }
}

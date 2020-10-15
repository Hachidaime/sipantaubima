<?php

use app\controllers\Controller;
use app\helper\Functions;
use app\models\PackageDetailModel;
use app\models\ContractModel;
use app\models\AddendumModel;

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

        if (!$_SESSION['USER']['usr_is_package']) {
            header('Location:' . BASE_URL . '/403');
        }
    }

    public function detail()
    {
        $pkgd_id = $_POST['pkgd_id'];
        list($detail) = $this->contractModel->singlearray([
            ['pkgd_id', $pkgd_id],
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
                $detail['cnt_plan_pho_date'],
            )
            : '';

        list($addendum, $addendum_c) = $this->addendumModel->multiarray([
            ['pkgd_id', $pkgd_id],
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
                        $row['add_plan_pho_date'],
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
                $data['cnt_plan_pho_date'],
            )
            : null;
        $data['cnt_value'] = !empty($data['cnt_value'])
            ? str_replace(',', '.', $data['cnt_value'])
            : 0;

        $addendum = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $addendum[$key] = $value;
                unset($data[$key]);
            } else {
                if (empty($value)) {
                    unset($data[$key]);
                }
            }
        }

        if ($this->validate($data)) {
            $result = $this->contractModel->save($data);

            $tag = 'Ubah';
            $id = $data['id'];

            if ($result) {
                $this->submitAddendum($addendum);
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
            'cnt_contractor_name' => 'required|max:250',
            'cnt_no' => 'required|max:25',
            'cnt_date' => 'required|date',
            'cnt_wsw_date' => 'required|date',
            'cnt_days' => 'required',
            'cnt_plan_pho_date' => 'required|date',
            'cnt_value' => 'required',
            'cnt_consultant_name' => 'required',
        ]);

        $validation->setAliases([
            'cnt_contractor_name' => 'Nama Kontraktor',
            'cnt_no' => 'Nomor Kontrak',
            'cnt_date' => 'Tanggal Kontrak',
            'cnt_wsw_date' => 'Tanggal SPMK',
            'cnt_days' => 'Waktu Pelaksanaan',
            'cnt_plan_pho_date' => 'Tanggal Rencana PHO',
            'cnt_value' => 'Nilai Kontrak',
            'cnt_consultant_name' => 'Nama Konsultan',
        ]);

        $validation->setMessages([
            'required' => '<strong>:attribute</strong> harus diisi.',
            'max' => '<strong>:attribute</strong> maximum :max karakter.',
            'date' => 'Format <strong>:attribute</strong> tidak valid.',
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

    private function submitAddendum($data)
    {
        for ($i = 1; $i <= 8; $i++) {
            $addendum = [
                'pkgd_id' => $_POST['pkgd_id'],
                'id' => $data['add_id'][$i],
                'add_no' => strtoupper($data['add_no'][$i]),
                'add_order' => $data['add_order'][$i],
                'add_date' => !empty($data['add_date'][$i])
                    ? Functions::dateFormat(
                        'd/m/Y',
                        'Y-m-d',
                        $data['add_date'][$i],
                    )
                    : '',
                'add_days' => $data['add_days'][$i],
                'add_plan_pho_date' => !empty($data['add_plan_pho_date'][$i])
                    ? Functions::dateFormat(
                        'd/m/Y',
                        'Y-m-d',
                        $data['add_plan_pho_date'][$i],
                    )
                    : '',
                'add_value' => !empty($data['add_value'][$i])
                    ? str_replace(',', '.', $data['add_value'][$i])
                    : '',
            ];

            foreach ($addendum as $key => $value) {
                if (empty($value)) {
                    unset($addendum[$key]);
                }
            }

            $this->addendumModel->save($addendum);
        }
    }
}

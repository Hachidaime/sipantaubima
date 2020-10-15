<?php

use app\controllers\Controller;
use app\helper\Flasher;
use app\helper\Functions;
use app\models\LogModel;
use app\models\UserModel;

class ProfileController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setControllerAttribute(__CLASS__);
        $this->smarty->assign('title', $this->title);

        $this->userModel = new UserModel();
        $this->logModel = new LogModel();
    }

    public function index()
    {
        $this->smarty->assign('breadcrumb', [[$this->title, '']]);

        $this->smarty->display("{$this->directory}/index.tpl");
    }

    public function search()
    {
        list($list, $info) = $this->logModel->userActivity($_POST);

        foreach ($list as $idx => $row) {
            $row['created_at'] = Functions::dateFormat(
                'Y-m-d H:i:s',
                'd/m/Y H.i.s',
                $row['created_at'],
            );

            $list[$idx] = $row;
        }

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
    public function form()
    {
        $this->smarty->display("{$this->directory}/form.tpl");
    }

    public function detail()
    {
        list($detail) = $this->userModel->singlearray($_POST['id']);
        unset($detail['usr_password']);

        echo json_encode($detail);
        exit();
    }

    public function submit()
    {
        $data = $_POST;
        list($detail) = $this->userModel->singlearray($data['id']);

        $data['usr_is_master'] = $detail['usr_is_master'];
        $data['usr_is_package'] = $detail['usr_is_package'];
        $data['usr_is_progress'] = $detail['usr_is_progress'];
        $data['usr_is_report'] = $detail['usr_is_report'];

        $data['usr_password'] = !empty($data['usr_password'])
            ? Functions::encrypt($data['usr_password'])
            : '';
        // var_dump($data);
        // exit();
        if ($this->validate($data)) {
            if (empty($data['usr_password'])) {
                unset($data['usr_password']);
            }

            $result = $this->userModel->save($data);
            if ($data['id'] > 0) {
                $tag = 'Ubah';
                $id = $data['id'];
            } else {
                $tag = 'Tambah';
                $id = $result;
            }

            if ($result) {
                if ($id == $_SESSION['USER']['id']) {
                    $this->setUserSession($detail);
                }

                Flasher::setFlash(
                    "Berhasil {$tag} {$this->title}.",
                    $this->name,
                    'success',
                );
                $this->writeLog(
                    "{$tag} {$this->title}",
                    "{$tag} {$this->title} [{$id}] berhasil.",
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
        $rules = [
            'usr_name' => 'required',
            'usr_username' => "required|max:20|min:3|unique:{$this->userModel->getTable()},id,{$data['id']}",
        ];

        if (empty($data['id'])) {
            $rules['usr_password'] = 'required|max:20|min:6';
        }

        $validation = $this->validator->make($data, $rules);

        $validation->setAliases([
            'usr_name' => 'Nama',
            'usr_username' => 'Username',
            'usr_password' => 'Password',
        ]);

        $validation->setMessages([
            'required' => '<strong>:attribute</strong> harus diisi.',
            'unique' => '<strong>:attribute</strong> sudah ada di database.',
            'min' => '<strong>:attribute</strong> minimum :min karakter.',
            'max' => '<strong>:attribute</strong> maximum :max karakter.',
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
}

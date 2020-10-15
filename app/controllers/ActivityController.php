<?php

use app\controllers\Controller;
use app\helper\Flasher;
use app\models\ActivityModel;

/**
 * @desc this class will handle Uang controller
 *
 * @class BankController
 * @extends Controller
 * @author Hachidaime
 */

class ActivityController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setControllerAttribute(__CLASS__);
        $this->title = 'Kegiatan';
        $this->smarty->assign('title', $this->title);

        $this->activityModel = new ActivityModel();

        if (!$_SESSION['USER']['usr_is_master']) {
            header('Location:' . BASE_URL . '/403');
        }
    }

    public function index()
    {
        $this->smarty->assign('breadcrumb', [
            ['Master', ''],
            [$this->title, ''],
        ]);

        $this->smarty->assign('subtitle', "Daftar {$this->title}");

        $this->smarty->display("{$this->directory}/index.tpl");
    }

    public function search(int $page = 1, string $keyword = null)
    {
        $page = $_POST['page'] ?? 1;
        $keyword = $_POST['keyword'] ?? null;

        list($list, $info) = $this->activityModel->paginate(
            $page,
            [['act_code', 'LIKE', "%{$keyword}%"]],
            [['act_code', 'ASC']],
        );
        $info['keyword'] = $keyword;

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
            list(, $count) = $this->activityModel->singlearray($id);
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
            ['Master', ''],
            [$this->title, $this->lowerName],
            [$tag, ''],
        ]);

        $this->smarty->assign('subtitle', "{$tag} {$this->title}");

        $this->smarty->display("{$this->directory}/form.tpl");
    }

    public function detail()
    {
        list($detail, $count) = $this->activityModel->singlearray($_POST['id']);

        echo json_encode($detail);
        exit();
    }

    public function submit()
    {
        $data = $_POST;
        $data['act_code'] = strtoupper(trim($data['act_code']));
        if ($this->validate($data)) {
            $result = $this->activityModel->save($data);
            if ($data['id'] > 0) {
                $tag = 'Ubah';
                $id = $data['id'];
            } else {
                $tag = 'Tambah';
                $id = $result;
            }

            if ($result) {
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
        $validation = $this->validator->make($data, [
            'act_code' => "required|max:20|min:3|unique:{$this->activityModel->getTable()},id,{$data['id']}",
            'act_name' => 'required|max:100|min:6',
        ]);

        $validation->setAliases([
            'act_code' => 'Kode Kegiatan',
            'act_name' => 'Nama Kegiatan',
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

    public function remove()
    {
        $id = (int) $_POST['id'];
        $tag = 'Hapus';
        $result = $this->activityModel->delete($id);

        if ($result) {
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

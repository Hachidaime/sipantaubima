<?php
use app\helper\Flasher;
use app\controllers\Controller;
use app\models\UserModel;

class LoginController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->smarty->assign('title', 'Log In');
    }

    /**
     * @desc this method will handle default Home page
     *
     * @method index
     */
    public function login()
    {
        if (isset($_SESSION['USER'])) {
            header('Location: ' . BASE_URL);
        }
        $this->smarty->display('Login/login.tpl');
    }

    public function submit()
    {
        if ($this->validate()) {
            list($detail) = $this->userModel->singlearray([
                ['usr_username', $_POST['usr_username']],
            ]);

            $this->setUserSession($detail);

            $_SESSION['FISCAL_YEAR'] = $_POST['fiscal_year'];

            $this->writeLog('User Login', "User {$detail['usr_name']} login.");
            Flasher::setFlash(
                "Selamat Datang {$detail['usr_name']}",
                'LoginController',
                'success',
            );

            echo json_encode(['success' => true]);
            exit();
        }
    }

    private function validate()
    {
        $validation = $this->validator->make($_POST, [
            'fiscal_year' => 'required',
            'usr_username' => 'required',
            'usr_password' => 'required|login:' . $_POST['usr_username'],
        ]);

        $validation->setAliases([
            'fiscal_year' => 'Tahun Anggaran',
            'usr_username' => 'Username',
            'usr_password' => 'Password',
        ]);

        $validation->setMessages([
            'required' => '<strong>:attribute</strong> harus diisi.',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $result = [
                'success' => false,
                'msg' => $validation->errors()->firstOfAll(),
            ];
            echo json_encode($result);
            exit();
        }
        return true;
    }

    public function logout()
    {
        $this->writeLog(
            'User Logout',
            "User {$_SESSION['USER']['usr_name']} logout.",
        );
        session_destroy();
        header('Location: ' . BASE_URL);
    }
}

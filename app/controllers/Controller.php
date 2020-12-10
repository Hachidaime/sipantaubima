<?php
namespace app\controllers;

use PDO;
use Rakit\Validation\Validator;
use app\rules\UniqueRule;
use app\rules\LoginRule;
use app\rules\UniqPkgActRule;
use app\rules\UniqPkgdNoRule;
use app\helper\Flasher;
use app\helper\Functions;
use app\rules\UniqTrgRule;
use app\rules\UniqProgRule;
use app\models\LogModel;

/**
 * Class Controller
 *
 * Controller menyediakan tempat nyaman
 * untuk memuat komponen dan melakukan fungsi
 * yang dibutuhkan oleh semua Controller.
 *
 * Extends Class ini dalam Controller baru:
 *
 *   class Home extends Controller
 *
 * Untuk keamanan pastikan untuk menyatakan setiap method baru sebagai protected atau private.
 *
 * PHP VERSION 7
 *
 * @package Rakit
 * @see https://github.com/rakit/validation
 * @package Smarty
 * @see https://www.smarty.net/
 * @author Hachidaime
 */
class Controller
{
    protected $title;
    protected $name;
    protected $lowerName;
    protected $directory;

    /**
     * function __construct
     *
     * Constuctor
     *
     * @access public
     */
    public function __construct()
    {
        global $smarty;

        $this->validator = new Validator();

        $dbConfig = [
            'development' => DEVELOPMENT_DB, // ? Localhost
            'production' => PRODUCTION_DB // ? Server
        ];

        $pdo = new PDO(
            'mysql:host=' .
                $dbConfig[ENVIRONMENT]['host'] .
                ';dbname=' .
                $dbConfig[ENVIRONMENT]['database'] .
                ';charset=utf8',
            $dbConfig[ENVIRONMENT]['username'],
            $dbConfig[ENVIRONMENT]['password']
        );

        $this->validator->addValidator('unique', new UniqueRule($pdo));
        $this->validator->addValidator('login', new LoginRule());
        $this->validator->addValidator('uniq_pkg_act', new UniqPkgActRule());
        $this->validator->addValidator('uniq_pkgd_no', new UniqPkgdNoRule());
        $this->validator->addValidator('uniq_trg', new UniqTrgRule());
        $this->validator->addValidator('uniq_prog', new UniqProgRule());

        $this->smarty = &$smarty;

        $this->smarty->assign('flash', Flasher::getFlash());

        $this->logModel = new LogModel();
    }

    public function setControllerAttribute(string $class)
    {
        $class_arr = Functions::splitCamelCase(
            str_replace('Controller', '', $class)
        );
        $this->title = implode(' ', $class_arr);
        $this->name = implode('', $class_arr);
        $this->lowerName = strtolower($this->name);
        $this->directory = implode('', $class_arr);
    }

    /**
     * function model
     *
     * Fungsi ini akan menangani model.
     *
     * @access protected
     * @param string $model nama model
     * @return object $model
     */
    protected function model(string $model)
    {
        require_once "app/models/{$model}.php";
        return new $model();
    }

    /**
     * function pagination
     *
     * Fungsi ini akan menangani pembuatan pagination.
     *
     * @access protected
     * @param array $data
     */
    protected function pagination(array $data)
    {
        $data['pageMin'] = $data['currentPage'] - SURROUND_COUNT;
        $data['pageMin'] = $data['pageMin'] > 0 ? $data['pageMin'] : 1;

        $data['pageMax'] = $data['currentPage'] + SURROUND_COUNT;
        $data['pageMax'] =
            $data['pageMax'] < $data['lastPage']
                ? $data['pageMax']
                : $data['lastPage'];

        $data['uri'] =
            BASE_URL .
            Functions::getStringBetween(
                rtrim($_SERVER['REQUEST_URI'], '/'),
                BASE_PATH,
                '/page'
            );

        $this->smarty->assign('paging', $data);
        $this->smarty->assign(
            'pager',
            $this->smarty->fetch('Templates/pagination.tpl')
        );
    }

    /**
     * function writeLog
     *
     * Fungsi ini akan menangani penulisan log.
     *
     * @access protected
     * @param string $type is log type
     * @param string $description is description log
     */
    protected function writeLog(string $type, string $description)
    {
        $data = array_merge(
            [
                'log_type' => $type,
                'log_description' => $description
            ],
            Functions::getCreated(),
            Functions::getRemoteIp()
        );
        $this->logModel->save($data);
    }

    /**
     * function error404
     *
     * Fungsi ini menampilkan halaman Error Not Found
     *
     * @access protected
     */
    public function error404()
    {
        $this->smarty->assign('title', 'Page Not Found');
        $this->smarty->display('Page/404.tpl');
    }

    /**
     * function change
     *
     * Fungsi ini mengubah sistem
     *
     * @access public
     * @param string $slug slug sistem
     */
    public function change(string $slug)
    {
        list($system, $count) = $this->model('SystemModel')->get([
            ['sys_slug', $slug]
        ]);

        if ($count == 0) {
            Flasher::setFlash('System tidak ditemukan!', 'change', 'error');
        } else {
            unset($_SESSION['SYSTEM']);
            $_SESSION['SYSTEM'] = $system;
        }
        header('Location:' . BASE_URL);
    }

    public function setUserSession($detail)
    {
        foreach ($detail as $key => $value) {
            if (
                !in_array($key, [
                    'id',
                    'usr_name',
                    'usr_consultant_name',
                    'usr_is_master',
                    'usr_is_package',
                    'usr_is_progress',
                    'usr_is_report'
                ])
            ) {
                unset($detail[$key]);
            }
        }
        $_SESSION['USER'] = $detail;
    }
}

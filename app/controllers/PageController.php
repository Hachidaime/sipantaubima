<?php
use app\controllers\Controller;
use app\helper\Flasher;

class PageController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setControllerAttribute(__CLASS__);
    }

    public function error403()
    {
        header('X-PHP-Response-Code: 403', true, 403);
        $this->smarty->assign('title', 'Forbidden');
        $this->smarty->display("{$this->directory}/403.tpl");
    }

    public function error404()
    {
        header('X-PHP-Response-Code: 404', true, 404);
        $this->smarty->assign('title', 'Not Found');
        $this->smarty->display("{$this->directory}/404.tpl");
    }

    public function actionResult()
    {
        $data = Flasher::getFlash();
        if (is_null($data)) {
            header('Location: ' . BASE_URL);
        }

        switch ($data['type']) {
            case 'success':
                $data['color'] = 'success';
                $data['icon'] = '
          <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
          </svg>
        ';
                break;

            default:
                $data['color'] = 'danger';
                $data['icon'] = '
          <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-x-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path fill-rule="evenodd" d="M11.854 4.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708-.708l7-7a.5.5 0 0 1 .708 0z"/>
            <path fill-rule="evenodd" d="M4.146 4.146a.5.5 0 0 0 0 .708l7 7a.5.5 0 0 0 .708-.708l-7-7a.5.5 0 0 0-.708 0z"/>
          </svg>
        ';
                break;
        }

        $this->smarty->assign('data', $data);
        return $this->smarty->display('Page/result.php');
    }

    public function underConstruction()
    {
        $this->smarty->assign('title', 'Not Found');
        $this->smarty->display("{$this->directory}/uc.tpl");
    }
}

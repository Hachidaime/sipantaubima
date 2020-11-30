<?php

use app\controllers\Controller;

/**
 * Class HomeController
 *
 * This class will handle Home controller
 *
 * @author Hachidaime
 */

class HomeController extends Controller
{
    /**
     * __construct
     *
     * @return void
     * @access public
     */
    public function __construct()
    {
        parent::__construct();
        $this->setControllerAttribute(__CLASS__);
        $this->smarty->assign('title', $this->title);
    }

    /**
     * function index
     *
     * This method will handle default Home page
     *
     * @access public
     */
    public function index()
    {
        $this->smarty->display('Home/index.tpl');
    }
}

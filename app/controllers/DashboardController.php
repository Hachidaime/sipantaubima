<?php

use app\controllers\Controller;
use app\models\DashboardModel;
use app\models\LogModel;

/**
 * Class DashboardController
 *
 * This class will handle User controller
 *
 * @author Hachidaime
 */

class DashboardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setControllerAttribute(__CLASS__);
        $this->smarty->assign('title', $this->title);

        $this->dashboardModel = new DashboardModel();
        $this->logModel = new LogModel();
    }

    /**
     * function index
     *
     * This method will handle default Dashboard page
     *
     * @access public
     */
    public function index()
    {
        // $activityInfo = $this->dashboardModel->activityInfo();

        // $this->smarty->assign('activityInfo', $activityInfo);
        $this->smarty->display('Dashboard/index.tpl');
    }

    /**
     * fucntion searchActivityInfo
     *
     * This method will search Activity Information
     *
     * @return void
     */
    public function searchActivityInfo()
    {
        $activityInfo = $this->dashboardModel->activityInfo();

        echo json_encode($activityInfo);
        exit();
    }
}

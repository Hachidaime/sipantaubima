<?php

namespace app\models;

use app\models\Model;

/**
 * @desc this class will handle Dashboard model
 *
 * @class UserModel
 * @author Hachidaime
 */
class DashboardModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->performanceReportModel = new PerformanceReportModel();
    }

    public function activityInfo()
    {
        $performance = $this->performanceReportModel->getData();
        // print '<pre>';
        // print_r($performance);
        // print '</pre>';

        $activityOpt = [];
        foreach ($performance as $row) {
            $red = 0;
            $yellow = 0;
            $green = 0;
            $finish = 0;
            if (!empty($row['detail'])) {
                foreach ($row['detail'] as $value) {
                    switch ($value['indicator']) {
                        case 'red':
                            $red += 1;
                            break;

                        case 'yellow':
                            $yellow += 1;
                            break;

                        case 'green':
                            $green += 1;
                            break;
                    }
                    if (!empty($value['pkgd_pho_date'])) {
                        $finish += 1;
                    }
                }
            }

            $activityOpt[$row['act_code']] = [
                'act_code' => $row['act_code'],
                'act_name' => $row['act_name'],
                'red' => $red,
                'yellow' => $yellow,
                'green' => $green,
                'finish' => $finish,
                'all' => !empty($row['detail']) ? count($row['detail']) : 0
            ];
        }

        $activityInfo = array_values($activityOpt);

        return $activityInfo;
    }
}

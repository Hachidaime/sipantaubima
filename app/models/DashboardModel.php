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
                if ($value['prog_physical'] == 100) {
                    $finish += 1;
                }
            }

            $activityOpt[$row['act_code']] = [
                'act_code' => $row['act_code'],
                'act_name' => $row['act_name'],
                'red' => $red,
                'yellow' => $yellow,
                'green' => $green,
                'finish' => $finish,
                'all' => count($row['detail']),
            ];
        }

        $activityInfo = array_values($activityOpt);
        /* 

        foreach ($activityInfo as $idx => $row) {
            $red = 0;
            $yellow = 0;
            $green = 0;
            $finish = 0;

            if (is_array($row['detail'])) {
                $row['all'] = count($row['detail']);

                foreach ($row['detail'] as $key => $value) {
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
                    if ($value['prog_physical'] == 100) {
                        $finish += 1;
                    }
                }
            }

            $row['red'] = $red;
            $row['yellow'] = $yellow;
            $row['green'] = $green;
            $row['finish'] = $finish;
            unset($row['detail']);
            $activityInfo[$idx] = $row;
        }

        */
        return $activityInfo;
    }
}

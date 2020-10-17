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
        $activityOpt = [];
        foreach ($performance as $perf) {
            $activityOpt[$perf['act_code']]['act_code'] = $perf['act_code'];
            $activityOpt[$perf['act_code']]['act_name'] = $perf['act_name'];
            foreach ($perf['detail'] as $perfd) {
                foreach ($perfd as $row) {
                    foreach ($row as $key => $value) {
                        if (!in_array($key, ['indicator', 'prog_physical'])) {
                            unset($row[$key]);
                        }
                    }
                    $row['prog_physical'] = str_replace(
                        ',',
                        '.',
                        $row['prog_physical'],
                    );
                    $activityOpt[$perf['act_code']]['detail'][] = $row;
                }
            }
        }

        $activityInfo = array_values($activityOpt);

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

        return $activityInfo;
    }
}

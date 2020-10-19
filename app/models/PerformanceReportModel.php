<?php
namespace app\models;

use app\helper\Functions;
use app\models\Model;
use app\models\PackageDetailModel;
use app\models\PackageModel;
use app\models\TargetModel;
use app\models\ProgressModel;
use app\models\ProgramModel;
use app\models\ActivityModel;

/**
 * @desc this class will handle Program model
 *
 * @class UserModel
 * @author Hachidaime
 */
class PerformanceReportModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->packageModel = new PackageModel();
        $this->packageDetailModel = new PackageDetailModel();
        $this->targetModel = new TargetModel();
        $this->progressModel = new ProgressModel();
        $this->contractModel = new ContractModel();
        $this->programModel = new ProgramModel();
        $this->activityModel = new ActivityModel();
    }

    public function getData($data = null)
    {
        list($program) = $this->programModel->multiarray(null, [
            ['prg_code', 'ASC'],
        ]);
        $programOptions = Functions::listToOptions(
            $program,
            'prg_code',
            'prg_name',
        );

        list($activity) = $this->activityModel->multiarray(null, [
            ['act_code', 'ASC'],
        ]);
        $activityOptions = Functions::listToOptions(
            $activity,
            'act_code',
            'act_name',
        );

        $where = [];
        if (!empty($data['pkg_fiscal_year'])) {
            $where[] = ['pkg_fiscal_year', $data['pkg_fiscal_year']];
        }
        if (!empty($data['prg_code'])) {
            $where[] = ['prg_code', $data['prg_code']];
        }
        if (!empty($data['act_code'])) {
            $where[] = ['act_code', $data['act_code']];
        }
        $where = !empty($where) ? $where : null;
        list($package, $packageCount) = $this->packageModel->multiarray($where);

        if ($packageCount > 0) {
            $pkgIdList = implode(
                ',',
                array_map(function ($val) {
                    return $val['id'];
                }, $package),
            );

            $targetOpt = $this->getTargetOpt($pkgIdList, $data['pkgd_id']);
            $progressOpt = $this->getProgressOpt($pkgIdList, $data['pkgd_id']);

            // var_dump($targetOpt);
            // var_dump($progressOpt);

            foreach ($package as $idx => $row) {
                // var_dump($row);
                $row['prg_name'] = $programOptions[$row['prg_code']];
                $row['act_name'] = $activityOptions[$row['act_code']];

                $targetOpt[$row['id']] = $targetOpt[$row['id']] ?? [];
                $progressOpt[$row['id']] = $progressOpt[$row['id']] ?? [];

                $target = [];
                foreach ($targetOpt[$row['id']] as $value) {
                    $target[$value['id']][] = $value;
                }
                $target = array_values($target);

                $progress = [];
                foreach ($progressOpt[$row['id']] as $key => $value) {
                    $progress[$value['id']][] = $value;
                }
                $progress = array_values($progress);

                $packageDetail = [];
                for ($i = 0; $i < count($target); $i++) {
                    if (count($target[$i]) > count($progress[$i])) {
                        foreach ($target[$i] as $key => $value) {
                            $progress[$i][$key] = is_array($progress[$i][$key])
                                ? $progress[$i][$key]
                                : [];

                            $detail = array_merge(
                                $target[$i][$key],
                                $progress[$i][$key],
                            );

                            $detail = $this->getDetail($detail);
                            if ($detail['week'] > 1) {
                                $detail['pkgd_no'] = '';
                                $detail['pkgd_name'] = '';
                                $detail['cnt_value'] = '';
                                $detail['pkgd_last_prog_date'] = '';
                            }

                            $packageDetail[$i][$key] = $detail;
                        }
                    } else {
                        foreach ($progress[$i] as $key => $value) {
                            $target[$i][$key] = is_array($target[$i][$key])
                                ? $target[$i][$key]
                                : [];

                            $detail = array_merge(
                                $target[$i][$key],
                                $progress[$i][$key],
                            );

                            $detail = $this->getDetail($detail);
                            if ($detail['week'] > 1) {
                                $detail['pkgd_no'] = '';
                                $detail['pkgd_name'] = '';
                                $detail['cnt_value'] = '';
                                $detail['pkgd_last_prog_date'] = '';
                            }
                            $packageDetail[$i][$key] = $detail;
                        }
                    }
                }

                $row['detail'] = $packageDetail;
                $package[$idx] = $row;
            }
        }

        return $package;
    }

    public function getDetail($detail)
    {
        $detail['trg_finance_pct'] =
            ($detail['trg_finance'] / $detail['cnt_value']) * 100;

        $detail['prog_finance_pct'] =
            ($detail['prog_finance'] / $detail['cnt_value']) * 100;

        $detail['devn_physical'] =
            $detail['prog_physical'] - $detail['trg_physical'];
        $detail['devn_finance'] =
            $detail['prog_finance'] - $detail['trg_finance'];
        $detail['devn_finance_pct'] =
            ($detail['devn_finance'] / $detail['cnt_value']) * 100;

        $indicator = 'white';
        if (!is_null($detail['trg_physical'])) {
            if (
                ($detail['trg_physical'] >= 0 &&
                    $detail['trg_physical'] <= 70 &&
                    $detail['devn_physical'] > -10) ||
                ($detail['trg_physical'] > 70 &&
                    $detail['trg_physical'] <= 100 &&
                    $detail['devn_physical'] > -5)
            ) {
                $indicator = 'red';
            } elseif (
                ($detail['trg_physical'] >= 0 &&
                    $detail['trg_physical'] <= 70 &&
                    $detail['devn_physical'] >= 0 &&
                    $detail['devn_physical'] <= 10) ||
                ($detail['trg_physical'] > 70 &&
                    $detail['trg_physical'] <= 100 &&
                    $detail['devn_physical'] >= 0 &&
                    $detail['devn_physical'] <= 5)
            ) {
                $indicator = 'yellow';
            } elseif (
                ($detail['trg_physical'] >= 0 &&
                    $detail['trg_physical'] <= 70 &&
                    $detail['devn_physical'] > 0) ||
                ($detail['trg_physical'] > 70 &&
                    $detail['trg_physical'] <= 100 &&
                    $detail['devn_physical'] > 0)
            ) {
                $indicator = 'green';
            }
        }

        return [
            'pkgd_id' => $detail['id'],
            'pkgd_no' => $detail['pkgd_no'],
            'pkgd_name' => $detail['pkgd_name'],
            'cnt_value' =>
                $detail['cnt_value'] > 0
                    ? number_format($detail['cnt_value'], 2, ',', '.')
                    : '',
            'week' =>
                $detail['trg_week'] > 0
                    ? $detail['trg_week']
                    : ($detail['prog_week'] > 0
                        ? $detail['prog_week']
                        : ''),
            'pkgd_last_prog_date' => !is_null($detail['pkgd_last_prog_date'])
                ? Functions::dateFormat(
                    'Y-m-d',
                    'd/m/Y',
                    $detail['pkgd_last_prog_date'],
                )
                : '',
            'trg_physical' =>
                $detail['trg_physical'] > 0
                    ? number_format($detail['trg_physical'], 2, ',', '.')
                    : '',
            'trg_finance_pct' =>
                $detail['trg_finance_pct'] > 0
                    ? number_format($detail['trg_finance_pct'], 2, ',', '.')
                    : '',
            'prog_physical' =>
                $detail['prog_physical'] > 0
                    ? number_format($detail['prog_physical'], 2, ',', '.')
                    : '',
            'prog_finance_pct' =>
                $detail['prog_finance_pct'] > 0
                    ? number_format($detail['prog_finance_pct'], 2, ',', '.')
                    : '',
            'devn_physical' =>
                !empty($detail['trg_physical']) ||
                !empty($detail['prog_physical'])
                    ? number_format($detail['devn_physical'], 2, ',', '.')
                    : '',
            'devn_finance_pct' =>
                !empty($detail['trg_finance']) ||
                !empty($detail['prog_finance'])
                    ? number_format($detail['devn_finance_pct'], 2, ',', '.')
                    : '',
            'indicator' => $indicator,
        ];
    }

    private function getTargetOpt($pkgIdList, $pkgd_id = null)
    {
        $filter =
            $pkgd_id > 0
                ? "AND `{$this->packageDetailModel->getTable()}`.`id` = {$pkgd_id}"
                : '';

        $table_left = $this->packageDetailModel->getTable();
        $table_right = $this->targetModel->getTable();
        $table_contract = $this->contractModel->getTable();
        $select = "
            `{$table_left}`.`id`,
            `{$table_left}`.`pkg_id`,
            `{$table_left}`.`pkgd_no`,
            `{$table_left}`.`pkgd_name`,
            `{$table_left}`.`pkgd_last_prog_date`,
            `{$table_right}`.`trg_week`,
            `{$table_right}`.`trg_physical`,
            `{$table_right}`.`trg_finance`,
            `{$table_contract}`.`cnt_value`
        ";
        $join = "`{$table_right}`
            ON `{$table_right}`.`pkgd_id` = `{$table_left}`.`id`";
        $join_contract = "`{$table_contract}`
            ON `{$table_contract}`.`pkgd_id` = `{$table_left}`.`id`";
        $where = "WHERE `{$table_left}`.`pkg_id` IN ({$pkgIdList})
            {$filter}";

        $query = "SELECT {$select} FROM `{$table_left}` 
            LEFT JOIN {$join} 
            LEFT JOIN {$join_contract}
            {$where}
            UNION 
            SELECT {$select} FROM `{$table_left}` 
            RIGHT JOIN {$join} 
            LEFT JOIN {$join_contract}
            {$where}";
        $target = $this->db->query($query)->toArray();
        // echo nl2br($query);

        $targetOpt = [];
        foreach ($target as $row) {
            $targetOpt[$row['pkg_id']][] = $row;
        }

        return $targetOpt;
    }

    private function getProgressOpt($pkgIdList, $pkgd_id = null)
    {
        $filter =
            $pkgd_id > 0
                ? "AND `{$this->packageDetailModel->getTable()}`.`id` = {$pkgd_id}"
                : '';

        $table_left = $this->packageDetailModel->getTable();
        $table_right = $this->progressModel->getTable();
        $table_contract = $this->contractModel->getTable();
        $select = "
            `{$table_left}`.`id`,
            `{$table_left}`.`pkg_id`,
            `{$table_left}`.`pkgd_no`,
            `{$table_left}`.`pkgd_name`,
            `{$table_left}`.`pkgd_last_prog_date`,
            `{$table_right}`.`prog_week`,
            `{$table_right}`.`prog_physical`,
            `{$table_right}`.`prog_finance`,
            `{$table_contract}`.`cnt_value`
        ";
        $join = "`{$table_right}`
            ON `{$table_right}`.`pkgd_id` = `{$table_left}`.`id`";
        $join_contract = "`{$table_contract}`
            ON `{$table_contract}`.`pkgd_id` = `{$table_left}`.`id`";
        $where = "WHERE `{$table_left}`.`pkg_id` IN ({$pkgIdList})
            {$filter}";

        $query = "SELECT {$select} FROM `{$table_left}` 
            LEFT JOIN {$join} 
            LEFT JOIN {$join_contract}
            {$where}
            UNION 
            SELECT {$select} FROM `{$table_left}` 
            RIGHT JOIN {$join} 
            LEFT JOIN {$join_contract}
            {$where}";
        $progress = $this->db->query($query)->toArray();

        $progressOpt = [];
        foreach ($progress as $row) {
            $progressOpt[$row['pkg_id']][] = $row;
        }

        return $progressOpt;
    }
}

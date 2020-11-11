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

                $last_target = [];
                for ($i = 0; $i < count($target); $i++) {
                    if (is_array($target[$i])) {
                        $avg_trg_physical = 0;
                        $avg_trg_finance = 0;
                        if (is_array($target[$i])) {
                            $count_target = count($target[$i]);
                            foreach ($target[$i] as $value) {
                                $avg_trg_physical +=
                                    $count_target > 0
                                        ? $value['trg_physical'] / $count_target
                                        : 0;
                                $avg_trg_finance +=
                                    $count_target > 0
                                        ? $value['trg_finance'] / $count_target
                                        : 0;
                            }
                        }

                        $value['avg_trg_physical'] = $avg_trg_physical;
                        $value['avg_trg_finance'] = $avg_trg_finance;
                        $last_target[$i] = $value;
                    }
                }

                $last_progress = [];
                for ($i = 0; $i < count($progress); $i++) {
                    if (is_array($progress[$i])) {
                        $avg_prog_physical = 0;
                        $avg_prog_finance = 0;
                        if (is_array($progress[$i])) {
                            $count_progress = count($progress[$i]);
                            foreach ($progress[$i] as $value) {
                                $avg_prog_physical +=
                                    $count_progress > 0
                                        ? $value['prog_physical'] /
                                            $count_progress
                                        : 0;
                                $avg_prog_finance +=
                                    $count_progress > 0
                                        ? $value['prog_finance'] /
                                            $count_progress
                                        : 0;
                            }
                        }

                        $value['avg_prog_physical'] = $avg_prog_physical;
                        $value['avg_prog_finance'] = $avg_prog_finance;
                        $last_progress[$i] = $value;
                    }
                }

                $packageDetail = array_replace_recursive(
                    $last_target,
                    $last_progress,
                );

                $sub_trg_physical = 0;
                $sub_trg_finance_pct = 0;
                $sub_prog_physical = 0;
                $sub_prog_finance_pct = 0;
                if (is_array($packageDetail)) {
                    $count_package_Detail = count($packageDetail);

                    foreach ($packageDetail as $key => $value) {
                        $sub_trg_physical +=
                            $count_package_Detail > 0
                                ? $value['avg_trg_physical'] /
                                    $count_package_Detail
                                : 0;
                        $sub_trg_finance_pct +=
                            $count_package_Detail > 0
                                ? $value['avg_trg_finance_pct'] /
                                    $count_package_Detail
                                : 0;
                        $sub_prog_physical +=
                            $count_package_Detail > 0
                                ? $value['avg_prog_physical'] /
                                    $count_package_Detail
                                : 0;
                        $sub_prog_finance_pct +=
                            $count_package_Detail > 0
                                ? $value['avg_prog_finance_pct'] /
                                    $count_package_Detail
                                : 0;

                        $value = $this->getDetail($value);
                        $packageDetail[$key] = $value;
                    }
                }

                $row['sub_trg_physical'] = $sub_trg_physical;
                $row['sub_trg_finance_pct'] = $sub_trg_finance_pct;
                $row['sub_prog_physical'] = $sub_prog_physical;
                $row['sub_prog_finance_pct'] = $sub_prog_finance_pct;
                $row['detail'] = $packageDetail;

                $package[$idx] = $row;
            }
        }

        return $package;
    }

    public function getDetail($detail)
    {
        foreach ($detail as $key => $value) {
            $key = Functions::camelize($key);
            $$key = $value;
        }

        $trgFinancePct = $cntValue > 0 ? ($trgFinance / $cntValue) * 100 : 0;
        $avgTrgFinancePct =
            $cntValue > 0 ? ($avgTrgFinance / $cntValue) * 100 : 0;

        $progFinancePct = $cntValue > 0 ? ($progFinance / $cntValue) * 100 : 0;
        $avgProgFinancePct =
            $cntValue > 0 ? ($avgProgFinance / $cntValue) * 100 : 0;

        $devnPhysical = $progPhysical - $trgPhysical;
        $devnFinance = $progFinance - $trgFinance;
        $devnFinancePct = $cntValue > 0 ? ($devnFinance / $cntValue) * 100 : 0;

        $indicator = 'white';
        if (!is_null($trgPhysical)) {
            if (
                ($trgPhysical >= 0 &&
                    $trgPhysical <= 70 &&
                    $devnPhysical > -10) ||
                ($trgPhysical > 70 && $trgPhysical <= 100 && $devnPhysical > -5)
            ) {
                $indicator = 'red';
            } elseif (
                ($trgPhysical >= 0 &&
                    $trgPhysical <= 70 &&
                    $devnPhysical >= 0 &&
                    $devnPhysical <= 10) ||
                ($trgPhysical > 70 &&
                    $trgPhysical <= 100 &&
                    $devnPhysical >= 0 &&
                    $devnPhysical <= 5)
            ) {
                $indicator = 'yellow';
            } elseif (
                ($trgPhysical >= 0 &&
                    $trgPhysical <= 70 &&
                    $devnPhysical > 0) ||
                ($trgPhysical > 70 && $trgPhysical <= 100 && $devnPhysical > 0)
            ) {
                $indicator = 'green';
            }
        }

        return [
            'pkgd_id' => $id,
            'pkgd_no' => $pkgdNo,
            'pkgd_name' => $pkgdName,
            'cnt_value' =>
                $cntValue > 0 ? number_format($cntValue, 2, ',', '.') : '',
            'pkgd_last_prog_date' => !is_null($pkgdLastProgDate)
                ? Functions::dateFormat('Y-m-d', 'd/m/Y', $pkgdLastProgDate)
                : '',
            'trg_physical' =>
                $trgPhysical > 0
                    ? number_format($trgPhysical, 2, ',', '.')
                    : '',
            'trg_finance_pct' =>
                $trgFinancePct > 0
                    ? number_format($trgFinancePct, 2, ',', '.')
                    : '',
            'avg_trg_physical' =>
                $avgTrgPhysical > 0
                    ? number_format($avgTrgPhysical, 2, ',', '.')
                    : '',
            'avg_trg_finance_pct' =>
                $avgTrgFinancePct > 0
                    ? number_format($avgTrgFinancePct, 2, ',', '.')
                    : '',
            'prog_physical' =>
                $progPhysical > 0
                    ? number_format($progPhysical, 2, ',', '.')
                    : '',
            'prog_finance_pct' =>
                $progFinancePct > 0
                    ? number_format($progFinancePct, 2, ',', '.')
                    : '',
            'avg_prog_physical' =>
                $avgProgPhysical > 0
                    ? number_format($avgProgPhysical, 2, ',', '.')
                    : '',
            'avg_prog_finance_pct' =>
                $avgProgFinancePct > 0
                    ? number_format($avgProgFinancePct, 2, ',', '.')
                    : '',
            'devn_physical' =>
                !empty($trgPhysical) || !empty($progPhysical)
                    ? number_format($devnPhysical, 2, ',', '.')
                    : '',
            'devn_finance_pct' =>
                !empty($trgFinance) || !empty($progFinance)
                    ? number_format($devnFinancePct, 2, ',', '.')
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

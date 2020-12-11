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
class ProgressReportModel extends Model
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
            ['prg_code', 'ASC']
        ]);
        $programOptions = Functions::listToOptions(
            $program,
            'prg_code',
            'prg_name'
        );

        list($activity) = $this->activityModel->multiarray(null, [
            ['act_code', 'ASC']
        ]);
        $activityOptions = Functions::listToOptions(
            $activity,
            'act_code',
            'act_name'
        );

        $where = [];
        $where[] = ['pkg_fiscal_year', $data['pkg_fiscal_year']];
        if ($data['prg_code'] != '') {
            $where[] = ['prg_code', $data['prg_code']];
        }
        if ($data['act_code'] != '') {
            $where[] = ['act_code', $data['act_code']];
        }

        list($package, $packageCount) = $this->packageModel->multiarray($where);

        if ($packageCount > 0) {
            $pkgIdList = implode(
                ',',
                array_map(function ($val) {
                    return $val['id'];
                }, $package)
            );

            $targetOpt = $this->getTargetOpt($pkgIdList, $data['pkgd_id']);
            $progressOpt = $this->getProgressOpt($pkgIdList, $data['pkgd_id']);

            foreach ($package as $idx => $row) {
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
                    $target_count = count($target[$i]);
                    $progress_count = count($progress[$i]);

                    if ($target_count > $progress_count) {
                        foreach ($target[$i] as $key => $value) {
                            $progress[$i][$key] = is_array($progress[$i][$key])
                                ? $progress[$i][$key]
                                : [];

                            $detail = array_merge(
                                $target[$i][$key],
                                $progress[$i][$key]
                            );

                            $detail['last_key'] =
                                $key == $progress_count - 1 ? true : false;

                            $detail = $this->getDetail($detail);

                            $packageDetail[$i][$key] = $detail;
                        }
                    } else {
                        foreach ($progress[$i] as $key => $value) {
                            $target[$i][$key] = is_array($target[$i][$key])
                                ? $target[$i][$key]
                                : $target[$i][$key - 1];

                            $detail = array_merge(
                                $target[$i][$key],
                                $progress[$i][$key]
                            );

                            $detail['last_key'] =
                                $key == $progress_count - 1 ? true : false;

                            $detail = $this->getDetail($detail);

                            $packageDetail[$i][$key] = $detail;
                        }
                    }

                    $trgFinanceCum = 0;
                    $progFinanceCum = 0;
                    foreach ($packageDetail[$i] as $key => $detail) {
                        $cntValue = Functions::floatValue($detail['cnt_value']);
                        $cntValueEnd = Functions::floatValue(
                            $detail['cnt_value_end']
                        );

                        $trgFinanceCum += Functions::floatValue(
                            $detail['trg_finance']
                        );

                        $trgFinancePct =
                            $cntValue > 0
                                ? ($trgFinanceCum / $cntValue) * 100
                                : 0;

                        $progFinanceCum += Functions::floatValue(
                            $detail['prog_finance']
                        );

                        $progFinancePct = !empty($cntValueEnd)
                            ? ($progFinanceCum / $cntValueEnd) * 100
                            : (!empty($cntValue)
                                ? ($progFinanceCum / $cntValue) * 100
                                : 0);

                        $devnFinancePct = $progFinancePct - $trgFinancePct;

                        $detail = array_merge($detail, [
                            'trg_finance_pct' => Functions::commaDecimal(
                                $trgFinancePct
                            ),
                            'prog_finance_pct' => Functions::commaDecimal(
                                $progFinancePct
                            ),
                            'devn_finance_pct' => Functions::commaDecimal(
                                $devnFinancePct
                            )
                        ]);

                        if ($detail['week'] > 1) {
                            $detail['pkgd_no'] = '';
                            $detail['pkgd_name'] = '';
                            $detail['cnt_value'] = '';
                            $detail['cnt_value_end'] = '';
                            $detail['pkgd_debt_ceiling'] = '';
                        }

                        $packageDetail[$i][$key] = $detail;
                    }
                }

                $row['detail'] = $packageDetail;
                $package[$idx] = $row;
                if (is_null($packageDetail) || empty($packageDetail)) {
                    unset($package[$idx]);
                }
            }
        }

        $package = array_values($package);

        return $package;
    }

    public function getDetail($detail)
    {
        foreach ($detail as $key => $value) {
            $key = Functions::camelize($key);
            $$key = $value;
        }

        $query = "SELECT * FROM apm_addendum 
            WHERE add_value > 0 
            AND pkgd_id = {$detail['id']} 
            ORDER BY add_order DESC 
            LIMIT 1";

        $addendum = $this->db->query($query)->first();
        $addendum = !empty($addendum) ? $addendum->toArray() : $addendum;

        $cntValueEnd = $this->db->getCount() > 0 ? $addendum['add_value'] : 0;

        $devnPhysical = $progPhysical - $trgPhysical;

        $indicator = 'white';

        if ($lastKey) {
            if (!is_null($trgPhysical)) {
                if (
                    ($trgPhysical >= 0 &&
                        $trgPhysical <= 70 &&
                        $devnPhysical > -10) ||
                    ($trgPhysical > 70 &&
                        $trgPhysical <= 100 &&
                        $devnPhysical > -5)
                ) {
                    $indicator = 'red';
                } elseif (
                    ($trgPhysical >= 0 &&
                        $trgPhysical <= 70 &&
                        $devnPhysical >= -10 &&
                        $devnPhysical <= 0) ||
                    ($trgPhysical > 70 &&
                        $trgPhysical <= 100 &&
                        $devnPhysical >= -5 &&
                        $devnPhysical <= 0)
                ) {
                    $indicator = 'yellow';
                } elseif (
                    ($trgPhysical >= 0 &&
                        $trgPhysical <= 70 &&
                        $devnPhysical > 0) ||
                    ($trgPhysical > 70 &&
                        $trgPhysical <= 100 &&
                        $devnPhysical > 0)
                ) {
                    $indicator = 'green';
                }
            }
        }

        $result = [
            'pkgd_id' => $id,
            'pkgd_no' => $pkgdNo,
            'pkgd_name' => $pkgdName,
            'cnt_value' =>
                $cntValue > 0 ? number_format($cntValue, 2, ',', '.') : '',
            'cnt_value_end' =>
                $cntValueEnd > 0
                    ? number_format($cntValueEnd, 2, ',', '.')
                    : '',
            'pkgd_debt_ceiling' =>
                $pkgdDebtCeiling > 0
                    ? number_format($pkgdDebtCeiling, 2, ',', '.')
                    : '',
            'week' =>
                $trgWeek > 0 ? $trgWeek : ($progWeek > 0 ? $progWeek : ''),
            'pkgd_last_prog_date' => !is_null($pkgdLastProgDate)
                ? Functions::dateFormat('Y-m-d', 'd/m/Y', $pkgdLastProgDate)
                : '',
            'trg_date' => !is_null($trgDate)
                ? Functions::dateFormat('Y-m-d', 'd/m/Y', $trgDate)
                : '',
            'trg_physical' => number_format((float) $trgPhysical, 2, ',', '.'),
            'trg_finance' => $trgFinance,
            'prog_physical' => number_format(
                (float) $progPhysical,
                2,
                ',',
                '.'
            ),
            'prog_finance' => $progFinance,
            'devn_physical' => !empty($progPhysical)
                ? number_format($devnPhysical, 2, ',', '.')
                : '',
            'indicator' => !empty($progPhysical) ? $indicator : 'white'
        ];

        return $result;
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
            `{$table_left}`.`pkgd_debt_ceiling`,
            `{$table_left}`.`pkgd_last_prog_date`,
            `{$table_right}`.`trg_week`,
            `{$table_right}`.`trg_date`,
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
        foreach ($target as $idx => $row) {
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
            `{$table_left}`.`pkgd_debt_ceiling`,
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
        foreach ($progress as $idx => $row) {
            $progressOpt[$row['pkg_id']][] = $row;
        }

        return $progressOpt;
    }
}

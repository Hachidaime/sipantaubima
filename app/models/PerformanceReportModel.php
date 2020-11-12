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
        if (!empty($data['fiscal_year'])) {
            $where[] = ['pkg_fiscal_year', $data['fiscal_year']];
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

            $packageDetail = $this->getPackageDetail($pkgIdList, $data);

            foreach ($package as $idx => $row) {
                $row['prg_name'] = $programOptions[$row['prg_code']];
                $row['act_name'] = $activityOptions[$row['act_code']];

                foreach ($packageDetail[$row['id']] as $key => $value) {
                    $row['detail'][$key] = $this->getDetail($value);
                }

                $package[$idx] = $row;
            }

            /* foreach ($package as $idx => $row) {
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
            } */
        }

        return $package;
    }

    public function getDetail($detail)
    {
        $query = "SELECT * FROM apm_addendum 
            WHERE add_value > 0 
            AND pkgd_id = {$detail['id']} 
            ORDER BY add_order DESC 
            LIMIT 1";

        $addendum = $this->db->query($query)->first();
        $addendum = !empty($addendum) ? $addendum->toArray() : $addendum;

        foreach ($detail as $key => $value) {
            $key = Functions::camelize($key);
            $$key = $value;
        }

        $cntValueEnd =
            $this->db->getCount() > 0 ? $addendum['add_value'] : $cntValue;

        $trgFinancePct = $cntValue > 0 ? ($trgFinance / $cntValue) * 100 : 0;

        $progFinancePct =
            $cntValueEnd > 0
                ? ($progFinanceCum / $cntValueEnd) * 100
                : ($cntValue > 0
                    ? ($progFinanceCum / $cntValue) * 100
                    : 0);

        $devnPhysical = $progPhysical - $trgPhysical;
        $devnFinancePct = $progFinancePct - $trgFinancePct;

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

        $result = [
            'pkgd_id' => $id,
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
            'week' => $week,
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
            'prog_physical' =>
                $progPhysical > 0
                    ? number_format($progPhysical, 2, ',', '.')
                    : '',
            'prog_finance_pct' =>
                $progFinancePct > 0
                    ? number_format($progFinancePct, 2, ',', '.')
                    : '',
            'devn_physical' =>
                // !empty($trgPhysical) ||
                !empty($progPhysical)
                    ? number_format($devnPhysical, 2, ',', '.')
                    : '',
            'devn_finance_pct' =>
                // !empty($trgFinance) ||
                !empty($progFinancePct)
                    ? number_format($devnFinancePct, 2, ',', '.')
                    : '',
            'indicator' => $indicator,
        ];

        return $result;
    }

    private function getPackageDetail($pkgIdList, $data)
    {
        $packageDetailTable = $this->packageDetailModel->getTable();
        $progressTable = $this->progressModel->getTable();
        $targetTable = $this->targetModel->getTable();
        $contractTable = $this->contractModel->getTable();

        $query = "SELECT 
            `{$packageDetailTable}`.`id`,
            `{$packageDetailTable}`.`pkg_id`,
            `{$packageDetailTable}`.`pkgd_name`,
            `{$packageDetailTable}`.`pkgd_debt_ceiling`,
            `{$packageDetailTable}`.`pkgd_sum_prog_finance` as `prog_finance_cum`,
            `{$contractTable}`.`cnt_value`
            FROM `{$packageDetailTable}`
            LEFT JOIN `{$contractTable}` 
                ON `{$packageDetailTable}`.id = `{$contractTable}`.`pkgd_id`
            WHERE `pkg_id` IN ({$pkgIdList}) 
            ORDER BY pkgd_name";
        $packageDetail = $this->db->query($query)->toArray();

        $filter = [];

        $fiscalYear = $data['fiscal_year'] ?? $_SESSION['FISCAL_YEAR'];
        $fiscalMonth = $data['fiscal_month'];

        if ($fiscalYear) {
            $filter[] = "YEAR(prog_date) = '{$fiscalYear}'";
        }

        if ($fiscalMonth) {
            $filter[] = "MONTH(prog_date) = '{$fiscalMonth}'";
        }

        $filter = !empty($filter) ? 'AND ' . implode(' AND ', $filter) : '';

        $result = [];
        foreach ($packageDetail as $idx => $row) {
            if ($fiscalMonth != date('m') || $fiscalYear != date('Y')) {
                $query = "SELECT
                    SUM(`prog_finance`) as `prog_finance_cum`
                    FROM `{$progressTable}`
                    WHERE pkgd_id = '{$row['id']}'
                    {$filter}
                ";

                $progress = $this->db->query($query)->first();
                $progress = !empty($progress) ? $progress->toArray() : [];
                $row['prog_finance_cum'] = $progress['prog_finance_cum'];
            }

            $query = "SELECT
                `prog_week` as `week`,
                `prog_date` as pkgd_last_prog_date,
                `prog_physical` as `prog_physical`
                FROM `{$progressTable}`
                WHERE pkgd_id = '{$row['id']}'
                {$filter}
                AND `prog_week` = (
                    SELECT MAX(`prog_week`) 
                    FROM `{$progressTable}`
                    WHERE pkgd_id = '{$row['id']}'
                    {$filter}
                )
            ";

            $progress = $this->db->query($query)->first();
            $progress = !empty($progress) ? $progress->toArray() : [];

            $query = "SELECT 
                MAX(`trg_week`) as `trg_week`
                FROM `{$targetTable}`
                WHERE pkgd_id = '{$row['id']}'
            ";
            $lastTarget = $this->db
                ->query($query)
                ->first()
                ->toArray();

            $trgWeek =
                $lastTarget['trg_week'] >= $progress['week']
                    ? $progress['week']
                    : $lastTarget['trg_week'];

            $query = "SELECT
                `trg_date`,
                `trg_physical`,
                `trg_finance`
                FROM `{$targetTable}`
                WHERE pkgd_id = '{$row['id']}'
                AND `trg_week` = '{$trgWeek}'
            ";
            $target = $this->db->query($query)->first();
            $target = !empty($target) ? $target->toArray() : [];

            $result[$row['pkg_id']][] = array_merge($row, $progress, $target);
        }

        return $result;
    }
}

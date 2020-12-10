<?php
namespace app\models;

use app\helper\Functions;

/**
 * @desc this class will handle Performance Report model
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
        $this->progressReportModel = new ProgressReportModel();
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
        list($packages, $packagesCount) = $this->packageModel->multiarray(
            $where
        );

        if ($packagesCount > 0) {
            $pkgIdList = implode(
                ',',
                array_map(function ($val) {
                    return $val['id'];
                }, $packages)
            );

            list($progress, $progressCount) = $this->getProgressOpt(
                $pkgIdList,
                $data
            );

            if ($progressCount > 0) {
                $lastProgress = [];
                foreach ($progress as $idx => $row) {
                    $lastProgress['pkgd_id'][] = $row['pkgd_id'];
                    $lastProgress['prog_week'][$row['pkgd_id']] =
                        $row['prog_week'];
                }
                $lastProgress['pkgd_id'] = implode(
                    ',',
                    $lastProgress['pkgd_id']
                );

                list($target, $targetCount) = $this->getTargetOpt(
                    $lastProgress,
                    $data
                );

                $details = [];
                if ($targetCount > $progressCount) {
                    foreach ($target as $idx => $trg) {
                        $progress[$idx] =
                            is_null($progress[$idx]) ||
                            empty($progress[$idx]) ||
                            !is_array($progress[$idx])
                                ? []
                                : $progress[$idx];

                        $details[$idx] = array_merge(
                            $target[$idx],
                            $progress[$idx]
                        );
                    }
                } else {
                    foreach ($progress as $idx => $prog) {
                        $target[$idx] =
                            is_null($target[$idx]) ||
                            empty($target[$idx]) ||
                            !is_array($target[$idx])
                                ? []
                                : $target[$idx];

                        $details[$idx] = array_merge(
                            $target[$idx],
                            $progress[$idx]
                        );
                    }
                }

                $packagesDetail = [];
                foreach ($details as $idx => $detail) {
                    $detail = $this->getDetail($detail);
                    $packagesDetail[$detail['pkg_id']][] = $detail;
                }

                foreach ($packages as $idx => $package) {
                    $pkgDet = $packagesDetail[$package['id']];
                    if (!empty($pkgDet)) {
                        $sumCntValue = 0;
                        $sumCntValueEnd = 0;
                        $sumPkgdDebtCeiling = 0;
                        $sumTrgPhysical = 0;
                        $sumTrgFinancePct = 0;
                        $sumProgPhysical = 0;
                        $sumProgFinancePct = 0;
                        $sumDevnPhysical = 0;
                        $sumDevnFinancePct = 0;

                        $pkgDetCount = count($pkgDet);

                        foreach ($pkgDet as $key => $detail) {
                            $sumCntValue += floatval($detail['cnt_value']);
                            $sumCntValueEnd += floatval(
                                $detail['cnt_value_end']
                            );
                            $sumPkgdDebtCeiling += floatval(
                                $detail['pkgd_debt_ceiling']
                            );
                            $sumTrgPhysical += floatval(
                                $detail['trg_physical']
                            );
                            $sumTrgFinancePct += floatval(
                                $detail['trg_finance_pct']
                            );
                            $sumProgPhysical += floatval(
                                $detail['prog_physical']
                            );
                            $sumProgFinancePct += floatval(
                                $detail['prog_finance_pct']
                            );
                            $sumDevnPhysical += floatval(
                                $detail['devn_physical']
                            );
                            $sumDevnFinancePct += floatval(
                                $detail['devn_finance_pct']
                            );
                        }

                        $avgTrgPhysical =
                            $pkgDetCount > 0
                                ? $sumTrgPhysical / $pkgDetCount
                                : 0;
                        $avgTrgFinancePct =
                            $pkgDetCount > 0
                                ? $sumTrgFinancePct / $pkgDetCount
                                : 0;
                        $avgProgPhysical =
                            $pkgDetCount > 0
                                ? $sumProgPhysical / $pkgDetCount
                                : 0;
                        $avgProgFinancePct =
                            $pkgDetCount > 0
                                ? $sumProgFinancePct / $pkgDetCount
                                : 0;
                        $avgDevnPhysical =
                            $pkgDetCount > 0
                                ? $sumDevnPhysical / $pkgDetCount
                                : 0;
                        $avgDevnFinancePct =
                            $pkgDetCount > 0
                                ? $sumDevnFinancePct / $pkgDetCount
                                : 0;

                        $pkgDet[$key + 1] = [
                            'pkg_id' => '',
                            'pkgd_id' => '',
                            'pkgd_no' => '',
                            'pkgd_name' => 'Subtotal',
                            'cnt_value' => $sumCntValue > 0 ? $sumCntValue : '',
                            'cnt_value_end' =>
                                $sumCntValueEnd > 0 ? $sumCntValueEnd : '',
                            'pkgd_debt_ceiling' =>
                                $sumPkgdDebtCeiling > 0
                                    ? $sumPkgdDebtCeiling
                                    : '',
                            'week' => '',
                            'prog_date' => '',
                            'trg_physical' =>
                                $avgTrgPhysical > 0 ? $avgTrgPhysical : '',
                            'trg_finance_pct' =>
                                $avgTrgFinancePct > 0 ? $avgTrgFinancePct : '',
                            'prog_physical' =>
                                $avgProgPhysical > 0 ? $avgProgPhysical : '',
                            'prog_finance_pct' =>
                                $avgProgFinancePct > 0
                                    ? $avgProgFinancePct
                                    : '',
                            'devn_physical' => !empty($avgProgPhysical)
                                ? $avgDevnPhysical
                                : '',
                            'devn_finance_pct' => !empty($avgProgFinancePct)
                                ? $avgDevnFinancePct
                                : '',
                            'indicator' => 'white'
                        ];

                        $pkgDet = array_map(function ($detail) {
                            foreach ($detail as $key => $value) {
                                $detail[$key] = in_array($key, [
                                    'cnt_value',
                                    'cnt_value_end',
                                    'pkgd_debt_ceiling',
                                    'trg_physical',
                                    'trg_finance_pct',
                                    'prog_physical',
                                    'prog_finance_pct',
                                    'devn_physical',
                                    'devn_finance_pct'
                                ])
                                    ? number_format((float) $value, 2, ',', '.')
                                    : $value;
                            }
                            return $detail;
                        }, $pkgDet);
                    } /* else {
                        unset($packages[$idx]);
                    } */

                    $package = array_merge($package, [
                        'prg_name' => $programOptions[$package['prg_code']],
                        'act_name' => $activityOptions[$package['act_code']],
                        'detail' => $pkgDet
                    ]);

                    $packages[$idx] = $package;
                }
            }
        }

        $packages = array_values($packages);

        return $packages;
    }

    public function getDetail($detail)
    {
        foreach ($detail as $key => $value) {
            $key = Functions::camelize($key);
            $$key = $value;
        }

        $query = "SELECT * FROM apm_addendum 
            WHERE add_value > 0 
            AND pkgd_id = {$pkgdId} 
            ORDER BY add_order DESC 
            LIMIT 1";
        $addendum = $this->db->query($query)->first();
        $addendum = !empty($addendum) ? $addendum->toArray() : $addendum;

        $cntValueEnd =
            $this->db->getCount() > 0 ? $addendum['add_value'] : $cntValue;

        $trgFinancePct = $cntValue > 0 ? ($sumTrgFinance / $cntValue) * 100 : 0;

        $progFinancePct =
            $cntValueEnd > 0
                ? ($sumProgFinance / $cntValueEnd) * 100
                : ($cntValue > 0
                    ? ($sumProgFinance / $cntValue) * 100
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
            'pkg_id' => $pkgId,
            'pkgd_id' => $pkgdId,
            'pkgd_no' => $pkgdNo,
            'pkgd_name' => $pkgdName,
            'cnt_value' => $cntValue > 0 ? $cntValue : '',
            'cnt_value_end' => $cntValueEnd > 0 ? $cntValueEnd : '',
            'pkgd_debt_ceiling' => $pkgdDebtCeiling > 0 ? $pkgdDebtCeiling : '',
            'week' =>
                $trgWeek > 0 ? $trgWeek : ($progWeek > 0 ? $progWeek : ''),
            'prog_date' => !is_null($progDate)
                ? Functions::dateFormat('Y-m-d', 'd/m/Y', $progDate)
                : '',
            'pkgd_pho_date' => !is_null($pkgdPhoDate)
                ? Functions::dateFormat('Y-m-d', 'd/m/Y', $pkgdPhoDate)
                : '',
            'trg_physical' => $trgPhysical > 0 ? $trgPhysical : '',
            'trg_finance_pct' => $trgFinancePct > 0 ? $trgFinancePct : '',
            'prog_physical' => $progPhysical > 0 ? $progPhysical : '',
            'prog_finance_pct' => $progFinancePct > 0 ? $progFinancePct : '',
            'devn_physical' => !empty($progPhysical) ? $devnPhysical : '',
            'devn_finance_pct' => !empty($progFinancePct)
                ? $devnFinancePct
                : '',
            'indicator' => !empty($progPhysical) ? $indicator : 'white'
        ];

        return $result;
    }

    private function getTargetOpt($lastProgress, $data = null)
    {
        if (!is_null($data)) {
            foreach ($data as $key => $value) {
                $key = Functions::camelize($key);
                $$key = $value;
            }
        }

        $fiscalYear = $fiscalYear ?? $_SESSION['FISCAL_YEAR'];

        $filter = [];
        if ($fiscalYear) {
            $filter[] = "YEAR(trg_date) = '{$fiscalYear}'";
        }

        if ($fiscalMonth) {
            $filter[] = "MONTH(trg_date) = '{$fiscalMonth}'";
        }

        $filter = !empty($filter) ? 'AND ' . implode(' AND ', $filter) : '';

        $packageDetailTable = $this->packageDetailModel->getTable();
        $targetTable = $this->targetModel->getTable();
        $contractTable = $this->contractModel->getTable();

        $query = "SELECT 
            `{$packageDetailTable}`.`id` as `pkgd_id`,
            `{$packageDetailTable}`.`pkg_id`,
            `{$packageDetailTable}`.`pkgd_no`,
            `{$packageDetailTable}`.`pkgd_name`,
            `{$packageDetailTable}`.`pkgd_debt_ceiling`,
            `{$packageDetailTable}`.`pkgd_last_prog_date`,
            `{$packageDetailTable}`.`pkgd_pho_date`,
            `{$targetTable}`.`trg_week`,
            `{$targetTable}`.`trg_date`,
            `{$targetTable}`.`trg_physical`,
            `{$targetTable}`.`trg_finance`,
            `{$contractTable}`.`cnt_value`
            FROM `{$packageDetailTable}`     
            RIGHT JOIN `{$targetTable}`
                ON `{$targetTable}`.`pkgd_id` = `{$packageDetailTable}`.`id`
            LEFT JOIN `{$contractTable}`
                ON `{$contractTable}`.`pkgd_id` = `{$packageDetailTable}`.`id`
            WHERE `{$packageDetailTable}`.`id` IN ({$lastProgress['pkgd_id']})
            {$filter}";
        $trgPkg = $this->db->query($query)->toArray();

        $packages = [];
        foreach ($trgPkg as $idx => $trgs) {
            $packages[$trgs['pkgd_id']][] = $trgs;
        }

        $targetPackage = [];
        foreach ($packages as $targets) {
            $sumTrgFinance = 0;
            foreach ($targets as $idx => $target) {
                if (
                    $target['trg_week'] ==
                    $lastProgress['prog_week'][$target['pkgd_id']]
                ) {
                    $lastIdx = $idx;
                }

                $sumTrgFinance += $target['trg_finance'];
                $target = array_merge($target, [
                    'sum_trg_finance' => $sumTrgFinance
                ]);
                $targets[$idx] = $target;
            }

            $targetPackage[] = !is_null($targets[$lastIdx])
                ? $targets[$lastIdx]
                : $targets[$idx];
        }

        $targetPackageCount = count($targetPackage);

        return [$targetPackage, $targetPackageCount];
    }

    private function getProgressOpt($pkgIdList, $data = null)
    {
        if (!is_null($data)) {
            foreach ($data as $key => $value) {
                $key = Functions::camelize($key);
                $$key = $value;
            }
        }

        $fiscalYear = $fiscalYear ?? $_SESSION['FISCAL_YEAR'];

        $filter = [];
        if ($fiscalYear) {
            $filter[] = "YEAR(prog_date) = '{$fiscalYear}'";
        }

        if ($fiscalMonth) {
            $filter[] = "MONTH(prog_date) = '{$fiscalMonth}'";
        }

        $filter = !empty($filter) ? 'AND ' . implode(' AND ', $filter) : '';

        $packageDetailTable = $this->packageDetailModel->getTable();
        $progressTable = $this->progressModel->getTable();
        $contractTable = $this->contractModel->getTable();

        $query = "SELECT 
            `{$packageDetailTable}`.`id` as `pkgd_id`,
            `{$packageDetailTable}`.`pkg_id`,
            `{$packageDetailTable}`.`pkgd_no`,
            `{$packageDetailTable}`.`pkgd_name`,
            `{$packageDetailTable}`.`pkgd_debt_ceiling`,
            `{$packageDetailTable}`.`pkgd_last_prog_date`,
            `{$packageDetailTable}`.`pkgd_pho_date`,
            `{$progressTable}`.`prog_week`,
            `{$progressTable}`.`prog_date`,
            `{$progressTable}`.`prog_physical`,
            `{$progressTable}`.`prog_finance`,
            `{$contractTable}`.`cnt_value`
            FROM `{$packageDetailTable}`
            RIGHT JOIN `{$progressTable}`
                ON `{$progressTable}`.`pkgd_id` = `{$packageDetailTable}`.`id`
            LEFT JOIN `{$contractTable}`
                ON `{$contractTable}`.`pkgd_id` = `{$packageDetailTable}`.`id`
            WHERE `{$packageDetailTable}`.`pkg_id` IN ({$pkgIdList})
            {$filter}";

        $progPkg = $this->db->query($query)->toArray();

        $packages = [];
        foreach ($progPkg as $idx => $progs) {
            $packages[$progs['pkgd_id']][] = $progs;
        }

        $progressPackage = [];
        foreach ($packages as $progresses) {
            $sumProgFinance = 0;
            foreach ($progresses as $idx => $progress) {
                $sumProgFinance += $progress['prog_finance'];
                $progress = array_merge($progress, [
                    'sum_prog_finance' => $sumProgFinance
                ]);
                $progresses[$idx] = $progress;
                unset($progresses[$idx - 1]);
            }

            $progressPackage[] = $progresses[$idx];
        }

        $progressPackageCount = count($progressPackage);

        return [$progressPackage, $progressPackageCount];
    }
}

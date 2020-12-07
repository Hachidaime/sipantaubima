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
        $data['pkg_fiscal_year'] =
            $data['fiscal_year'] ?? $_SESSION['FISCAL_YEAR'];

        $packages = $this->progressReportModel->getData($data);

        foreach ($packages as $pkg => $package) {
            $packageDetails = [];

            foreach ($package['detail'] as $details) {
                foreach ($details as $idx => $detail) {
                    if (
                        $detail['indicator'] !== 'white' &&
                        !empty($detail['indicator'])
                    ) {
                        $detail = array_merge($detail, [
                            'pkgd_id' => $details[0]['pkgd_id'],
                            'pkgd_no' => $details[0]['pkgd_no'],
                            'pkgd_name' => $details[0]['pkgd_name'],
                            'cnt_value' => $details[0]['cnt_value'],
                            'cnt_value_end' => $details[0]['cnt_value_end'],
                            'pkgd_debt_ceiling' =>
                                $details[0]['pkgd_debt_ceiling'],
                            'pkgd_last_prog_date' =>
                                $details[0]['pkgd_last_prog_date']
                        ]);
                        $packageDetails[] = $detail;
                    }
                }
            }

            unset($package['detail']);

            $sumCntValue = 0;
            $sumCntValueEnd = 0;
            $sumPkgdDebtCeiling = 0;
            $sumTrgPhysical = 0;
            $sumTrgFinancePct = 0;
            $sumProgPhysical = 0;
            $sumProgFinancePct = 0;
            $sumDevnPhysical = 0;
            $sumDevnFinancePct = 0;

            $packageDetailsCount = count($packageDetails);

            foreach ($packageDetails as $pkgd => $packageDetail) {
                foreach ($packageDetail as $key => $val) {
                    if (
                        in_array($key, [
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
                    ) {
                        $packageDetail[$key] = Functions::floatValue($val);
                    }
                }

                $sumCntValue += $packageDetail['cnt_value'];
                $sumCntValueEnd += $packageDetail['cnt_value_end'];
                $sumPkgdDebtCeiling += $packageDetail['pkgd_debt_ceiling'];
                $sumTrgPhysical += $packageDetail['trg_physical'];
                $sumTrgFinancePct += $packageDetail['trg_finance_pct'];
                $sumProgPhysical += $packageDetail['prog_physical'];
                $sumProgFinancePct += $packageDetail['prog_finance_pct'];
                $sumDevnPhysical += $packageDetail['devn_physical'];
                $sumDevnFinancePct += $packageDetail['devn_finance_pct'];
            }

            $avgTrgPhysical =
                $packageDetailsCount > 0
                    ? $sumTrgPhysical / $packageDetailsCount
                    : 0;
            $avgTrgFinancePct =
                $packageDetailsCount > 0
                    ? $sumTrgFinancePct / $packageDetailsCount
                    : 0;
            $avgProgPhysical =
                $packageDetailsCount > 0
                    ? $sumProgPhysical / $packageDetailsCount
                    : 0;
            $avgProgFinancePct =
                $packageDetailsCount > 0
                    ? $sumProgFinancePct / $packageDetailsCount
                    : 0;
            $avgDevnPhysical =
                $packageDetailsCount > 0
                    ? $sumDevnPhysical / $packageDetailsCount
                    : 0;
            $avgDevnFinancePct =
                $packageDetailsCount > 0
                    ? $sumDevnFinancePct / $packageDetailsCount
                    : 0;

            $packageDetails[$pkgd + 1] = [
                'pkgd_id' => '',
                'pkgd_no' => '',
                'pkgd_name' => 'Subtotal',
                'cnt_value' => Functions::commaDecimal($sumCntValue),
                'cnt_value_end' => Functions::commaDecimal($sumCntValueEnd),
                'pkgd_debt_ceiling' => Functions::commaDecimal(
                    $sumPkgdDebtCeiling
                ),
                'week' => '',
                'pkgd_last_prog_date' => '',
                'trg_physical' => Functions::commaDecimal($avgTrgPhysical),
                'trg_finance_pct' => Functions::commaDecimal($avgTrgFinancePct),
                'prog_physical' => Functions::commaDecimal($avgProgPhysical),
                'prog_finance_pct' => Functions::commaDecimal(
                    $avgProgFinancePct
                ),
                'devn_physical' => Functions::commaDecimal($avgDevnPhysical),
                'devn_finance_pct' => Functions::commaDecimal(
                    $avgDevnFinancePct
                ),
                'indicator' => ''
            ];

            // echo '<pre>';
            // print_r($packageDetails);
            // echo '</pre>';

            $package['detail'] = $packageDetails;

            $packages[$pkg] = $package;
        }

        // echo '<pre>';
        // print_r($packages);
        // echo '</pre>';
        // exit();
        return $packages;
    }
    /* 
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
        list($package, $packageCount) = $this->packageModel->multiarray($where);

        if ($packageCount > 0) {
            $pkgIdList = implode(
                ',',
                array_map(function ($val) {
                    return $val['id'];
                }, $package)
            );

            $packageDetail = $this->getPackageDetail($pkgIdList, $data);

            foreach ($package as $idx => $row) {
                $row['prg_name'] = $programOptions[$row['prg_code']];
                $row['act_name'] = $activityOptions[$row['act_code']];

                if (is_array($packageDetail[$row['id']])) {
                    $packageDetailCount = count($packageDetail[$row['id']]);

                    $row['detail'] = [];

                    if ($packageDetailCount > 0) {
                        $avgTrgPhysical = 0;
                        $avgTrgFinancePct = 0;
                        $avgProgPhysical = 0;
                        $avgProgFinancePct = 0;
                        $avgDevnPhysical = 0;
                        $avgDevnFinancePct = 0;

                        $subCntValue = 0;
                        $subCntValueEnd = 0;
                        $subPkgdDebtCeiling = 0;

                        foreach ($packageDetail[$row['id']] as $key => $value) {
                            $detail = $this->getDetail($value);
                            $row['detail'][$key] = $detail;

                            $subCntValue += !empty($detail['cnt_value'])
                                ? $detail['cnt_value']
                                : 0;
                            $subCntValueEnd += !empty($detail['cnt_value_end'])
                                ? $detail['cnt_value_end']
                                : 0;
                            $subPkgdDebtCeiling += !empty(
                                $detail['pkgd_debt_ceiling']
                            )
                                ? $detail['pkgd_debt_ceiling']
                                : 0;

                            $avgTrgPhysical += number_format(
                                (!empty($detail['trg_physical'])
                                    ? $detail['trg_physical']
                                    : 0) / $packageDetailCount,
                                2
                            );
                            $avgTrgFinancePct += number_format(
                                (!empty($detail['trg_finance_pct'])
                                    ? $detail['trg_finance_pct']
                                    : 0) / $packageDetailCount,
                                2
                            );
                            $avgProgPhysical += number_format(
                                (!empty($detail['prog_physical'])
                                    ? $detail['prog_physical']
                                    : 0) / $packageDetailCount,
                                2
                            );
                            $avgProgFinancePct += number_format(
                                (!empty($detail['prog_finance_pct'])
                                    ? $detail['prog_finance_pct']
                                    : 0) / $packageDetailCount,
                                2
                            );
                            $avgDevnPhysical += number_format(
                                (!empty($detail['devn_physical'])
                                    ? $detail['devn_physical']
                                    : 0) / $packageDetailCount,
                                2
                            );
                            $avgDevnFinancePct += number_format(
                                (!empty($detail['devn_finance_pct'])
                                    ? $detail['devn_finance_pct']
                                    : 0) / $packageDetailCount,
                                2
                            );
                        }

                        if (!is_null($data)) {
                            $row['detail'][$key + 1] = [
                                'pkgd_name' => 'Subtotal',
                                'cnt_value' => !empty($subCntValue)
                                    ? $subCntValue
                                    : 0,
                                'cnt_value_end' => !empty($subCntValueEnd)
                                    ? $subCntValueEnd
                                    : 0,
                                'pkgd_debt_ceiling' => !empty(
                                    $subPkgdDebtCeiling
                                )
                                    ? $subPkgdDebtCeiling
                                    : 0,
                                'pkgd_last_prog_date' => '',
                                'trg_physical' => !empty($avgTrgPhysical)
                                    ? $avgTrgPhysical
                                    : 0,
                                'trg_finance_pct' => !empty($avgTrgFinancePct)
                                    ? $avgTrgFinancePct
                                    : 0,
                                'prog_physical' => !empty($avgProgPhysical)
                                    ? $avgProgPhysical
                                    : 0,
                                'prog_finance_pct' => !empty($avgProgFinancePct)
                                    ? $avgProgFinancePct
                                    : 0,
                                'devn_physical' => !empty($avgDevnPhysical)
                                    ? $avgDevnPhysical
                                    : 0,
                                'devn_finance_pct' => !empty($avgDevnFinancePct)
                                    ? $avgDevnFinancePct
                                    : 0
                            ];
                        }
                    }

                    foreach ($row['detail'] as $i => $r) {
                        foreach ($r as $k => $v) {
                            $r[$k] = in_array($k, [
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
                                ? ($v > 0
                                    ? number_format($v, 2, ',', '.')
                                    : '')
                                : $v;
                        }

                        $row['detail'][$i] = $r;
                    }
                }

                $package[$idx] = $row;
            }
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
            'cnt_value' => $cntValue > 0 ? $cntValue : 0,
            'cnt_value_end' => $cntValueEnd > 0 ? $cntValueEnd : 0,
            'pkgd_debt_ceiling' => $pkgdDebtCeiling > 0 ? $pkgdDebtCeiling : 0,
            'week' => $week,
            'pkgd_last_prog_date' => !is_null($pkgdLastProgDate)
                ? Functions::dateFormat('Y-m-d', 'd/m/Y', $pkgdLastProgDate)
                : '',
            'pkgd_pho_date' => !is_null($pkgdPhoDate)
                ? Functions::dateFormat('Y-m-d', 'd/m/Y', $pkgdPhoDate)
                : '',
            'trg_physical' => $trgPhysical > 0 ? $trgPhysical : 0,
            'trg_finance_pct' => $trgFinancePct > 0 ? $trgFinancePct : 0,
            'prog_physical' => $progPhysical > 0 ? $progPhysical : 0,
            'prog_finance_pct' => $progFinancePct > 0 ? $progFinancePct : 0,
            'devn_physical' => !empty($progPhysical) ? $devnPhysical : 0,
            'devn_finance_pct' => !empty($progFinancePct) ? $devnFinancePct : 0,
            'indicator' => $indicator
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
            `{$packageDetailTable}`.`pkgd_pho_date`,
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
    */
}

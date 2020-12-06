<?php
namespace app\models;

/**
 * @desc this class will handle Program model
 *
 * @class UserModel
 * @author Hachidaime
 */
class ProgressModel extends Model
{
    /**
     * Table name
     *
     * @var string
     * @access protected
     */
    protected $table = 'apm_progress';

    public function __construct()
    {
        parent::__construct();
        $this->programModel = new ProgramModel();
        $this->activityModel = new ActivityModel();
        $this->packageModel = new PackageModel();
        $this->packageDetailModel = new PackageDetailModel();
        $this->contractModel = new ContractModel();
    }

    public function getData($data, $paginage = false)
    {
        $page = $data['page'] ?? 1;
        $keyword = $data['keyword'] ?? null;

        $filter = [
            "WHERE `{$this->contractModel->getTable()}`.`usr_id` = '{$_SESSION['USER']['id']}'"
        ];
        if (!empty($keyword)) {
            $filter[] = "WHERE `{$this->table}`.`prog_fiscal_year` = '{$keyword}'";
        }

        $filter = implode(' ', $filter);

        $programTable = $this->programModel->getTable();
        $activityTable = $this->activityModel->getTable();
        $packageTable = $this->packageModel->getTable();
        $packageDetailTable = $this->packageDetailModel->getTable();
        $contractTable = $this->contractModel->getTable();

        $count = $this->db
            ->query(
                "SELECT 
                COUNT(*) as total_rows
                FROM `{$this->table}`
                LEFT JOIN `{$packageDetailTable}`
                    ON `{$packageDetailTable}`.`id` = `{$this->table}`.`pkgd_id`
                RIGHT JOIN `{$contractTable}`
                    ON `{$packageDetailTable}`.`id` = `{$contractTable}`.`pkgd_id`
                LEFT JOIN `{$packageTable}`
                    ON `{$packageTable}`.`id` = `{$packageDetailTable}`.`pkg_id`
                LEFT JOIN `{$programTable}`
                    ON `{$programTable}`.`prg_code` = `{$packageTable}`.`prg_code`
                LEFT JOIN `{$activityTable}`
                    ON `{$activityTable}`.`act_code` = `{$packageTable}`.`act_code`
                {$filter}"
            )
            ->toArray();

        $totalRows = $count[0]['total_rows'];

        $limit = ROWS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        $currentPage = $page;
        $lastPage = ceil($totalRows / $limit);
        $previousPage = $page - 1;
        $previousPage = $page != 1 ? $previousPage : null;
        $nextPage = $page + 1;
        $nextPage = $lastPage != $page ? $nextPage : null;

        $info = [
            'previousPage' => $previousPage,
            'currentPage' => $currentPage,
            'nextPage' => $nextPage,
            'lastPage' => $lastPage,
            'totalRows' => $totalRows
        ];

        $limit = $paginage ? "LIMIT {$limit} OFFSET {$offset}" : '';
        $query = "SELECT
            `{$programTable}`.`prg_name`,
            `{$activityTable}`.`act_name`,
            `{$packageDetailTable}`.`pkgd_name`,
            `{$this->table}`.*
            FROM `{$this->table}`
            LEFT JOIN `{$packageDetailTable}`
                ON `{$packageDetailTable}`.`id` = `{$this->table}`.`pkgd_id`
            RIGHT JOIN `{$contractTable}`
                ON `{$packageDetailTable}`.`id` = `{$contractTable}`.`pkgd_id`
            LEFT JOIN `{$packageTable}`
                ON `{$packageTable}`.`id` = `{$packageDetailTable}`.`pkg_id`
            LEFT JOIN `{$programTable}`
                ON `{$programTable}`.`prg_code` = `{$packageTable}`.`prg_code`
            LEFT JOIN `{$activityTable}`
                ON `{$activityTable}`.`act_code` = `{$packageTable}`.`act_code`
            {$filter}
            ORDER BY
            `{$packageTable}`.`pkg_fiscal_year` ASC,
            `{$programTable}`.`prg_name` ASC,
            `{$activityTable}`.`act_name` ASC,
            `{$packageDetailTable}`.`pkgd_name` ASC,
            `{$this->table}`.`prog_week` ASC
            {$limit}
            ";
        // echo nl2br($query);
        $list = $this->db->query($query)->toArray();

        return [$list, $info];
    }
}

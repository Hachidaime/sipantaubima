<?php
namespace app\models;

use app\models\Model;

class LogModel extends Model
{
    /**
     * Table name
     *
     * @var string
     * @access protected
     */
    protected $table = 'apm_log';

    public function userActivity($data)
    {
        $page = $data['page'] ?? 1;

        $list = $this->db
            ->table($this->table)
            ->orWhere([
                ['log_type', 'Tambah Target'],
                ['log_type', 'Ubah Target'],
                ['log_type', 'Hapus Target'],
                ['log_type', 'Tambah Progres Paket'],
                ['log_type', 'Ubah Progres Paket'],
                ['log_type', 'Hapus Progres Paket'],
            ])
            ->where([['created_by', $_SESSION['USER']['id']]])
            ->orderBy('created_at', 'DESC')
            ->paginate($page, ROWS_PER_PAGE);
        $list = !empty($list) ? $list->toArray() : $list;

        return [$list, $this->db->paginationInfo()];
    }

    /**
     * function lastActivity
     *
     * This method will handle to return last activity log
     *
     * @access public
     * @param int $limit the number of last logs displayed
     * @return array [list, list_count]
     */
    public function getLastActivity(int $limit = 5)
    {
        $list = $this->db
            ->table($this->table)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->toArray();

        return [$list, $this->db->paginationInfo()];
    }
}

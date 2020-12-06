<?php

namespace app\models;

use app\models\Model;

/**
 * @desc this class will handle Program model
 *
 * @class UserModel
 * @author Hachidaime
 */
class PackageDetailModel extends Model
{
    /**
     * Table name
     *
     * @var string
     * @access protected
     */
    protected $table = 'apm_package_detail';

    public function packageByUser($usr_id)
    {
        $contractModel = new ContractModel();

        $query = "SELECT `{$this->table}`.*, `{$contractModel->getTable()}`.`usr_id`
            FROM `{$this->table}`
            JOIN `{$contractModel->getTable()}`
                ON `{$contractModel->getTable()}`.`pkgd_id` = `{$this->table}`.`id`
            WHERE `{$contractModel->getTable()}`.`usr_id` = '{$usr_id}'
        ";

        $list = $this->db->query($query);
        $list = !empty($list) ? $list->toArray() : $list;
        return $list;
    }
}

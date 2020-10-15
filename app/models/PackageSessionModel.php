<?php

namespace app\models;

use app\models\Model;

/**
 * @desc this class will handle Program model
 *
 * @class UserModel
 * @author Hachidaime
 */
class PackageSessionModel extends Model
{
    /**
     * Table name
     *
     * @var string
     * @access protected
     */
    protected $table = 'apm_package_session';

    public function getPackageSessionId()
    {
        $data = $this->db
            ->table($this->table)
            ->get()
            ->first()
            ->toArray();

        $this->db->update(
            $this->table,
            ['pkgs_id' => $data['pkgs_id'] + 1],
            [['pkgs_id', $data['pkgs_id']]],
        );

        return $data['pkgs_id'];
    }
}

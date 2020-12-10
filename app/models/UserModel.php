<?php

namespace app\models;

use app\helper\Functions;
use app\models\Model;

/**
 * @desc this class will handle User model
 *
 * @class UserModel
 * @author Hachidaime
 */
class UserModel extends Model
{
    /**
     * Table name
     *
     * @var string
     * @access protected
     */
    protected $table = 'apm_user';

    public function __construct()
    {
        parent::__construct();
    }

    public function decrypt()
    {
        list($list) = $this->multiarray();

        foreach ($list as $row) {
            echo $row['id'] .
                ' - ' .
                $row['usr_username'] .
                ' - ' .
                Functions::decrypt($row['usr_password']) .
                ' - ' .
                $row['usr_consultant_name'] .
                '<br>';
        }
    }
}

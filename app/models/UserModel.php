<?php

namespace app\models;

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
}

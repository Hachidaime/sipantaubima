<?php
namespace app\rules;
use Rakit\Validation\Rule;
use app\models\UserModel;
use app\helper\Functions;

class LoginRule extends Rule
{
    protected $message = '<strong>Username</strong> dan <strong>Password</strong> tidak cocok.';

    protected $fillableParams = ['usr_username'];

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function check($value): bool
    {
        // make sure required parameters exists
        $this->requireParameters(['usr_username']);

        // getting parameters
        $username = $this->parameter('usr_username');
        $password = $value;

        list($detail) = $this->userModel->singlearray([
            ['usr_username', $username],
        ]);

        $count =
            $password == Functions::decrypt($detail['usr_password']) ? 1 : 0;

        // true for valid, false for invalid
        return $count !== 0;
    }
}

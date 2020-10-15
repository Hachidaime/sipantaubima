<?php

namespace app\rules;

use Rakit\Validation\Rule;
use app\models\ProgressModel;

class UniqProgRule extends Rule
{
    protected $message = ':attribute :value has been used';

    protected $fillableParams = ['pkgd_id', 'except'];

    protected $pdo;

    public function __construct()
    {
        $this->progressModel = new ProgressModel();
    }

    public function check($value): bool
    {
        // make sure required parameters exists
        $this->requireParameters(['pkgd_id', 'except']);

        // getting parameters
        $pkgd_id = $this->parameter('pkgd_id');
        $except = $this->parameter('except');
        $key = $this->getAttribute()->getKey();

        list(, $count) = $this->progressModel->singlearray([
            [$key, $value],
            ['pkgd_id', $pkgd_id],
            ['id', '!=', $except],
        ]);

        // true for valid, false for invalid
        return $count === 0;
    }
}

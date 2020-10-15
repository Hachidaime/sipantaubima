<?php

namespace app\rules;

use Rakit\Validation\Rule;
use app\models\PackageDetailModel;

class UniqPkgdNoRule extends Rule
{
    protected $message = ':attribute :value has been used';

    protected $fillableParams = ['pkgs_id', 'except'];

    protected $pdo;

    public function __construct()
    {
        $this->packageDetailModel = new PackageDetailModel();
    }

    public function check($value): bool
    {
        // make sure required parameters exists
        $this->requireParameters(['pkgs_id', 'except']);

        // getting parameters
        $pkgs_id = $this->parameter('pkgs_id');
        $except = $this->parameter('except');
        $key = $this->getAttribute()->getKey();

        list(, $count) = $this->packageDetailModel->singlearray([
            [$key, $value],
            ['pkgs_id', $pkgs_id],
            ['id', '!=', $except],
        ]);

        // true for valid, false for invalid
        return $count === 0;
    }
}

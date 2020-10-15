<?php

namespace app\rules;

use Rakit\Validation\Rule;
use app\models\PackageModel;

class UniqPkgActRule extends Rule
{
    protected $message = ':attribute :value has been used';

    protected $fillableParams = ['pkg_fiscal_year', 'prg_code', 'except'];

    protected $pdo;

    public function __construct()
    {
        $this->packageModel = new PackageModel();
    }

    public function check($value): bool
    {
        // make sure required parameters exists
        $this->requireParameters(['pkg_fiscal_year', 'prg_code', 'except']);

        // getting parameters
        $pkg_fiscal_year = $this->parameter('pkg_fiscal_year');
        $prg_code = $this->parameter('prg_code');
        $except = $this->parameter('except');
        $key = $this->getAttribute()->getKey();

        list(, $count) = $this->packageModel->singlearray([
            [$key, $value],
            ['pkg_fiscal_year', $pkg_fiscal_year],
            ['prg_code', $prg_code],
            ['id', '!=', $except],
        ]);

        // true for valid, false for invalid
        return $count === 0;
    }
}

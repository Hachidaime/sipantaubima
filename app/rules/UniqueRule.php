<?php

namespace app\rules;
use PDO;
use Rakit\Validation\Rule;

class UniqueRule extends Rule
{
    protected $message = ':attribute :value has been used';

    protected $fillableParams = ['table', 'column', 'except'];

    protected $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function check($value): bool
    {
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);

        // getting parameters
        $column = $this->parameter('column');
        $table = $this->parameter('table');
        $except = $this->parameter('except');
        $key = $this->getAttribute()->getKey();

        // do query
        $query = "select count(*) as count from `{$table}` where `{$key}` = :value and `{$column}` != :except";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':except', $except);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        // true for valid, false for invalid
        return intval($data['count']) === 0;
    }
}

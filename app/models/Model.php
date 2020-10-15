<?php

namespace app\models;

/**
 * Class Model
 *
 * Model menyediakan tempat nyaman
 * untuk memuat komponen dan melakukan fungsi
 * yang dibutuhkan oleh semua Model.
 *
 * Extends Class ini dalam Model baru:
 *
 *   class HomeModel extends Model
 *
 * Untuk keamanan pastikan untuk menyatakan setiap method baru
 * sebagai protected atau private.
 *
 * PHP VERSION 7
 *
 * @package DB
 * @see https://github.com/mareimorsy/DB
 * @author Hachidaime
 */
class Model
{
    /**
     * function __construct
     *
     * Constuctor
     *
     * @access public
     */
    public function __construct()
    {
        global $db;
        $this->db = &$db;
    }

    /**
     * function getTable
     *
     * Mendapatkan nama table
     *
     * @access public
     * @return string nama table
     */
    public function getTable()
    {
        return $this->table;
    }

    public function multiarray(
        $params = null,
        array $orders = null,
        bool $or = false
    ) {
        $list = $this->get($params, $orders, $or);
        $list = $list->get();
        $list = !empty($list) ? $list->toArray() : $list;
        return [$list, $this->db->getCount()];
    }

    public function singlearray($params = null)
    {
        $list = $this->get($params);
        $list = $list->get()->first();
        $list = !empty($list) ? $list->toArray() : $list;
        return [$list, $this->db->getCount()];
    }

    public function paginate(
        int $page = 1,
        array $params = null,
        array $orders = null,
        bool $or = false
    ) {
        $list = $this->get($params, $orders, $or);
        $list = $list->paginate($page, ROWS_PER_PAGE);
        $list = !empty($list) ? $list->toArray() : $list;
        return [$list, $this->db->paginationInfo()];
    }

    public function get($params = null, $orders = null, bool $or = false)
    {
        $result = $this->db->table($this->table);

        $result = is_null($params)
            ? $result->where([['id', '>', 0]])
            : ($or
                ? $result->orWhere($params)
                : $result->where($params));

        if (!is_null($orders)) {
            foreach ($orders as $value) {
                $result =
                    count($value) == 2
                        ? $result->orderBy($value[0], $value[1])
                        : $result->orderBy($value[0], 'ASC');
            }
        }

        return $result;
    }

    public function save($data = [])
    {
        $data = array_map(function ($item) {
            return trim($item);
        }, $data);

        if ($data['id'] > 0) {
            list($detail) = $this->singlearray([['id', $data['id']]]);
            foreach ($detail as $key => $value) {
                if (!in_array($key, array_keys($data))) {
                    unset($detail[$key]);
                }
            }

            $result =
                $detail == $data
                    ? 1
                    : ($result = $this->db->update(
                        $this->table,
                        $data,
                        $data['id'],
                    ));
        } else {
            unset($data['id']);
            $result = $this->db->insert($this->table, $data);
        }

        // var_dump($this->db);
        return $result;
    }

    public function delete($params)
    {
        $result = $this->db->delete($this->table, $params);
        return $result;
    }
}

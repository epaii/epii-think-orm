<?php
/**
 * Created by PhpStorm.
 * User: Lanxi
 * Date: 2019/5/1
 * Time: 17:32
 */

namespace epii\orm;


use think\Db;
use think\model\Collection;

class QuickQuery
{
    public static function query($table_name_or_query, $where, callable $row_callback = null, callable $callback = null)
    {
        if (is_string($table_name_or_query)) {
            $query = Db::name($table_name_or_query);

            $query->order("id desc");
        } else {
            $query = $table_name_or_query;

        }

        $list = $query->where($where)->limit(\epii\server\Args::params("offset"), \epii\server\Args::params("limit"))->select();
        $list = $row_callback ? array_map($row_callback, $list) : $list;
        if ($callback) {
            $list = $callback($list);
        }
        return $list;
    }


    public static function queryAndCount($table_name_or_query, $where, callable $row_callback = null, callable $callback = null){
        $query_count = null;
        if (is_string($table_name_or_query)) {
            $query = Db::name($table_name_or_query);
            $query_count = Db::name($table_name_or_query);
            $query->order("id desc");
        } else {
            $query = $table_name_or_query;
            $query_count = clone $table_name_or_query;
        }
        $count = $query_count->where($where)->count();
        $list = $query->where($where)->limit(\epii\server\Args::params("offset"), \epii\server\Args::params("limit"))->select();
        if ($list instanceof Collection) {
            $list = $list->all();
        }
        $outdata = ["rows" => $row_callback ? array_map($row_callback, $list) : $list, "total" => $count];
        if ($callback) {
            $outdata['rows'] = $callback($outdata['rows']);
        }
        return $outdata;
    }

}
<?php

namespace fastphp\base;

use fastphp\db\Sql;

class Model extends Sql
{
    protected $model;

    public function __construct()
    {
        //获取数据库表名
        if (!$this->table) {
            //获取模型名称
        }

    }
}
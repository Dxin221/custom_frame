<?php

namespace fastphp\db;

use \PDOStatement;

class Sql
{
    //数据库表名
    protected $table;

    //数据库主键
    protected $primary = 'id';

    //WHERE和ORDER拼装后的条件
    private $filter = '';

    //Pdo bindParam()绑定的参数集合
    private $param = array();

    /**
     * 查询条件拼接，使用方式：
     *
     * $this->where(['id = 1','and title=Web', ...])->fetch();
     * 为防止注入，建议通过$param方式传入参数：
     * $this->where(['id = :id'], [':id' => $id])->fetch();
     *
     * @param array $where 条件
     * @return $this 当前对象
     */
    public function where($where = array(), $param = array())
    {
        if ($where) {
            $this->filter .= ' WHERE ';
            $this->filter .= implode(' ', $where);

            $this->param = $param;
        }

        return $this;
    }

    /**
     * 拼装排序条件，使用方式：
     *
     * $this->order(['id DESC', 'title ASC', ...])->fetch();
     *
     * @param array $order 排序条件
     * @return $this
     */
    public function order($order = array())
    {
        if ($order) {
            $this->filter .= ' ORDER BY ';
            $this->filter .= implode(',', $order);
        }

        return $this;
    }

    //查询所有
    public function fetchAll()
    {
        $sql = sprintf("SELECT * FROM `%s` %s", $this->table, $this->filter);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->fetchAll();
    }

    //查询一条
    public function fetch()
    {
        $sql = sprintf("SELECT * FROM `%s` %s", $this->table, $this->filter);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->fetch();
    }

    //根据条件 (id) 删除
    public function delete($id)
    {
        $sql = sprintf("DELETE FROM `%s` WHERE `%s` = :%s", $this->table, $this->primary, $this->primary);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, array($this->primary => $id));
        $sth->execute();

        return $sth->rowCount();
    }

    //新增数据
    public function add($data)
    {
        $sql = sprintf("INSERT INTO `%s` %s", $this->table, $this->formatInsert($data));
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $data);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->rowCount();
    }

    //修改数据
    public function update($data)
    {
        $sql = sprintf("UPDATE `%s` SET %s %s", $this->table, $this->formatUpdate($data), $this->filter);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth, $data);
        $sth = $this->formatParam($sth, $this->param);
        $sth->execute();

        return $sth->rowCount();
    }

    /**
     * 占位符绑定具体的变量值
     * @param PDOStatement $sth 要绑定的PDOStatement对象
     * @param array $params 参数，有三种类型：
     * 1）如果SQL语句用问号?占位符，那么$params应该为
     *    [$a, $b, $c]
     * 2）如果SQL语句用冒号:占位符，那么$params应该为
     *    ['a' => $a, 'b' => $b, 'c' => $c]
     *    或者
     *    [':a' => $a, ':b' => $b, ':c' => $c]
     *
     * @return PDOStatement
     */
    public function formatParam(PDOStatement $sth, $params = array())
    {
        if (is_array($params)) {
            if (count($params) > 0) {
                foreach ($params as $param => &$value) {
                    $param = is_int($param) ? $param + 1 : ':' . ltrim($param, ':');
                    $sth->bindParam($param, $value);
                }
            }
        }

        return $sth;
    }

    //将数组转换成插入格式的sql语句
    private function formatInsert($data)
    {
        $fields = array();
        $names  = array();
        foreach ($data as $index => $datum) {
            $fields[] = sprintf("`%s`", $index);
            $names[]  = sprintf(":%s", $index);
        }

        $field = implode(',', $fields);
        $name  = implode(',', $names);

        return sprintf("(%s) VALUES (%s)", $field, $name);
    }

    //将数组转换成更新格式的sql语句
    private function formatUpdate($data)
    {
        $fields = array();
        foreach ($data as $index => $datum) {
            $fields[] = sprintf("`%s` = :%s", $index, $index);
        }

        return implode(',', $fields);
    }

}
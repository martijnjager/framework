<?php
namespace Database\Grammar;

trait Grammar
{
    public function compileSelect()
    {
        return 'select *';
    }

    public function compileSelectCount()
    {
        return 'select count(*)';
    }

    public function compileSelectCountColumn($column)
    {
        return "select count($column)";
    }

    public function compileFrom(...$tables)
    {
        return " from ". implode(', ', $tables[0]);
    }

    public function compileInsert($table, $columns, $values)
    {
        return "insert into $table ($columns) values ($values)";
    }

    public function compileSelectColumn(...$columns)
    {
        return 'select ' . implode(', ', $columns[0]);
    }

    public function compileJoin(array $values)
    {
        $sql = " ";

        foreach($values as $table => $items)
        {
            $sql .= "$items[type] join $table on $items[id] = $items[extern_id]";
        }

        return $sql;
    }

    public function compileWhere($wheres)
    {
        $where = " where ";
        foreach($wheres as $w)
        {
            $where .= "$w and ";
        }

        $where = substr($where, 0, strripos($where, "and") - 1);

        return $where;
    }

    public function compileEOS()
    {
        return ';';
    }

    public function compileLimit($limit)
    {
        return " limit $limit";
    }

    public function compileGroupBy($values)
    {
        return " group by " . $this->processArray($values[0]);
    }

    public function compileHaving($values)
    {
        return " having " . $this->processArray($values);
    }

    public function compileOrderBy($values)
    {
        return " order by " . $this->processAssocArray($values);
    }

    private function processArray($values)
    {
        $sql = "";

        foreach ($values as $key)
        {
            $sql .= "$key, ";
        }

        $sql = substr($sql, 0, strripos($sql, ", "));

        return $sql;
    }

    private function processAssocArray($values)
    {
        $sql = "";

        foreach($values as $key => $value)
        {
            $sql .= "$key $value, ";
        }

        $sql = substr($sql, 0, strripos($sql, ", "));

        return $sql;
    }

}

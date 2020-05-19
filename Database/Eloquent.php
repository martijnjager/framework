<?php
namespace Database;

use Collection\Collections;
use Database\Grammar\GrammarCompiler;
use Exception;

class Eloquent extends Saver
{
    private $clauses = [];

    private $params = [];

    public function select(...$columns)
    {
        if(empty($columns))
            $this->clauses['select'] = '*';
        else
            $this->clauses['select'] = $columns;

        return $this;
    }

    public function from($table)
    {
        $this->clauses['from'][] = $table;

        return $this;
    }

    public function join($table, $id, $extern_id)
    {
        return $this->addJoin($table, $id, $extern_id, "inner");
    }

    public function leftJoin($table, $id, $extern_id)
    {
        return $this->addJoin($table, $id, $extern_id, "left");
    }

    public function rightJoin($table, $id, $extern_id)
    {
        return $this->addJoin($table, $id, $extern_id, "right");
    }

    private function addJoin($table, $id, $extern_id, $type)
    {
        $this->clauses['join'][$table]['type'] = $type;
        $this->clauses['join'][$table]['id'] = $id;
        $this->clauses['join'][$table]['extern_id'] = $extern_id;

        return $this;
    }

    public function where($where, $operator, $value = null)
    {
        if(is_null($value))
        {
            $value = $operator;

            $this->clauses['where'][] = "$where = :w$where";
        }
        else{
            $this->clauses['where'][] = "$where $operator :w$where";
        }

        $this->addParameter("where", $where, $value);

        return $this;
    }

    public function groupBy(...$group)
    {
        $this->clauses['groupBy'][] = $group;

        return $this;
    }

    public function having($have, $operator, $value = null)
    {
        if(is_null($value))
        {
            $value = $operator;

            $this->clauses['having'][] = "$have = :h$have";
        }
        else{
            $this->clauses['having'][] = "$have $operator :h$have";
        }

        $this->addParameter("having", $have, $value);

        return $this;
    }

    public function orderBy()
    {
        $this->addOrder(func_get_args());

        return $this;
    }

    public function limit(int $amount)
    {
        $this->clauses['limit'] = $amount;

        return $this;
    }

    private function addOrder(array $values)
    {
        if(!is_array($values[0]))
        {
            for($i = 0; $i < count($values); $i++)
            {
                $this->clauses['orderBy'][$values[$i]] = $values[++$i];
            }
        }
        else
        {
            foreach($values as $key => $value)
            {
                $this->clauses['orderBy'][$key] = $value;
            }
        }
    }

    /**
     * @return Collections
     * @throws Exception
     */
    public function get()
    {
        $sql = $this->compileQuery();

        $this->runQuery($sql, $this->params);

        return $this->getResult()->all();
    }

    /**
     * @param $clause
     * @param $key
     * @param $value
     *
     * @return void
     */
    protected function addParameter($clause, $key, $value)
    {
        switch ($clause)
        {
            case "where": $this->params[":w$key"] = $value;
            break;

            case "having": $this->params[":h$key"] = $value;
            break;
        }
    }

    protected function compileQuery()
    {
        $this->handlePrecautions();
        
        return GrammarCompiler::compile($this->clauses);
    }

    private function handlePrecautions()
    {
        try {

            if(empty($this->clauses['from'])) {
                $name = $this->getTable();
                $this->from(strtolower($name));
            }

            if(empty($this->clauses['select']))
                $this->select();
        }
        catch(Exception $e) {

        }
    }
}

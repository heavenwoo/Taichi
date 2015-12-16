<?php

namespace Taichi\Database\Query\Grammar;

use Taichi\Database\Query\Builder;

class Grammar
{
    protected $components = [
        'columns',
        'table',
        'joins',
        'wheres',
        'groups',
        'havings',
        'orders',
        'limit',
        'offset',
        'unions',
        'lock',
    ];

    public function compileSelect(Builder $query)
    {
        return implode(' ', $this->compileComponents($query));
    }

    public function compileComponents(Builder $query)
    {
        $sql = [];

        foreach ($this->components as $component) {
            if (! is_null($query->$component)) {
                $method = 'compile' . ucfirst($component);
                $sql[$component] = $this->$method($query, $query->$component);
            }
        }

        return $sql;
    }

    public function compileColumns(Builder $query, $columns)
    {
        if (is_array($columns)) {

        }
        return 'SELECT ' . $columns[0];
    }

    public function compileTable(Builder $query, $table)
    {
        return 'FROM ' . $table;
    }

    public function compileJoins(Builder $query, $joins)
    {
        return 'JOIN ' . $joins;
    }

    public function compileWheres(Builder $query, $wheres)
    {
        $sql = [];

        foreach ($wheres as $where) {
            if (is_array($where['column'])) {
                foreach ($where['column'] as $column) {
                    $sql[] = $where['boolean'];
                    $sql[] = $column . $where['operator'] . '?';
                }
            } else {
                $sql[] = $where['boolean'];
                $sql[] = $where['column'] . $where['operator'] . '?';
            }
        }

        if (count($sql) > 0) {
            $sql = implode(' ', $sql);
            return 'WHERE ' . $this->removeLeadingBoolean($sql);
        }

        return '';
    }

    protected function removeLeadingBoolean($value)
    {
        return preg_replace('/AND |OR /i', '', $value, 1);
    }
}
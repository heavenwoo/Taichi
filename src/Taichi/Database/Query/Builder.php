<?php
namespace Taichi\Database\Query;

use Taichi\Database\Connection\PdoConnection;
use Taichi\Database\Query\Grammar\Grammar;
use Taichi\Exception\CoreException;

class Builder
{
    /**
     * @var Bee\Database\Connection\PdoConnection
     */
    protected $connection;

    /**
     * @var Bee\Database\Query\Grammar\Grammar
     */
    protected $grammar;

    protected $bindings = [
        'select' => [],
        'join'   => [],
        'where'  => [],
        'order'  => [],
        'union'  => [],
    ];

    public $columns;

    public $table;

    public $joins;

    public $wheres;

    public $groups;

    public $orders;

    public $limit;

    public $offset;

    public $unions;

    public $unionLimit;

    public $unionOffset;

    public $unionOrders;

    protected $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=',
        'LIKE', 'LIKE BINARY', 'NOT LIKE', 'BETWEEN', 'ILIKE',
        '&', '|', '^', '<<', '>>',
        'RLIKE', 'REGEXP', 'NOT REGEXP',
        '~', '~*', '!~', '!~*', 'SIMILAR TO',
        'NOT SIMILAR TO',
    ];

    /**
     * @var string table prefix
     */
    private $tb_prefix = '';

    public function __construct(PdoConnection $connection,
                                Grammar $grammar, $tb_prefix = '')
    {
        $this->connection = $connection;
        $this->grammar = $grammar;
        $this->tb_prefix = $tb_prefix;
    }

    /**
     * Set the columns to be selected.
     *
     * @param  array|mixed  $columns
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    public function table($table)
    {
        $this->table = $this->tb_prefix . $table;

        return $this;
    }

    public function where($column, $operator = null, $value = null, $boolean = 'AND')
    {
        if (func_num_args() == 2) {
            list($value, $operator) = [$operator, '='];
        }

        if (! in_array(strtoupper($operator), $this->operators, true)) {
            list($value, $operator) = [$operator, '='];
        }

        $this->wheres[] = compact('column', 'operator', 'value', 'boolean');

        $this->addBinding($value, 'where');

        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function join($table, $one, $operator = null, $two = null, $type = 'INNER')
    {
        //$this->addBinding('adfaf', 'join');
        return $this;
    }

    public function toSql()
    {
        return $this->grammar->compileSelect($this);
    }

    public function get($columns = ['*'])
    {
        if (is_null($this->columns)) {
            $this->columns = $columns;
        }

        return $this->connection->select($this->toSql(), $this->bindings);
    }

    public function addBinding($value, $type = 'where')
    {
        if (! array_key_exists($type, $this->bindings)) {
            throw new CoreException("Invalid binding type: {$type}.");
        }

        if (is_array($value)) {
            $this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
        } else {
            $this->bindings[$type][] = $value;
        }

        return $this;
    }
}
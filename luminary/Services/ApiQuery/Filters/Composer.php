<?php

namespace Luminary\Services\ApiQuery\Filters;

class Composer
{
    /**
     * Available filter operators
     *
     * @var array
     */
    protected $operators = ['=', '!=', '<>', '>', '<', '>=', '<=', '!<', '!>', 'IN', 'NOT IN', 'NULL', 'NOT NULL'];

    /**
     * Format a parsed query
     *
     * @param string $type
     * @param array $query
     * @return array|null
     */
    public static function format(string $type, array $query) :array
    {
        if (empty($query)) {
            return null;
        }

        $composer = new static;
        $method = camel_case('format_'.$type);

        return method_exists($composer, $method)
            ? $composer->{$method}($query)
            : $composer->formatDefault($type, $query);
    }

    /**
     * Return the default formatting for an
     * attribute query
     *
     * @param $type
     * @param $query
     * @return array
     */
    public function formatDefault($type, $query) :array
    {
        $attribute = $this->getAttribute($query);
        $operator = $this->getOperator($query);
        $value = $this->formatValue($query, $operator);

        return compact('attribute', 'operator', 'value', 'type');
    }

    /**
     * Return the formatting for a between query
     *
     * @param $type
     * @param $query
     * @return array
     */
    public static function formatBetween($type, $query)
    {
        return [
            'attribute' => $type,
            'operator' => null,
            'value' => $query,
            'type' => 'between'
        ];
    }

    /**
     * Return the formatting for an or between query
     *
     * @param $type
     * @param $query
     * @return array
     */
    public static function formatOrBetween($type, $query)
    {
        return [
            'attribute' => $type,
            'operator' => null,
            'value' => $query,
            'type' => 'or_between'
        ];
    }

    /**
     * Return the formatting for an has query
     *
     * @param $type
     * @param $query
     * @return array
     */
    public static function formatHas($type, $query) :array
    {
        return [
            'attribute' => $type,
            'operator' => null,
            'value' => $query,
            'type' => 'has'
        ];
    }

    /**
     * Return the formatting for a nested query
     *
     * @param $type
     * @param $query
     * @return array
     */
    public static function formatNested($type, $query) :array
    {
        return [
            'attribute' => $type,
            'operator' => null,
            'value' => $query,
            'type' => 'nested'
        ];
    }

    /**
     * Return the formatting for a nested query
     *
     * @param $type
     * @param $query
     * @return array
     */
    public static function formatOrNested($type, $query) :array
    {
        return [
            'attribute' => $type,
            'operator' => null,
            'value' => $query,
            'type' => 'or_nested'
        ];
    }

    /**
     * Get the attribute from a parsed query array
     *
     * @param array $query
     * @return string
     */
    protected function getAttribute(array &$query) :string
    {
        return array_shift($query);
    }

    /**
     * Get the operator from the parsed query array
     *
     * @param array $query
     * @return string
     */
    protected function getOperator(array &$query) :string
    {
        if (in_array(head($query), $this->operators)) {
            return array_shift($query);
        } elseif (is_string(head($query))) {
            return '=';
        } else {
            return 'IN';
        }
    }

    /**
     * Format a value
     *
     * @param $value
     * @param string $operator
     * @return array|string
     */
    protected function formatValue($value, string $operator)
    {
        if (is_array($value)) {
            return $this->formatArrayValue($value, $operator);
        }

        return $value;
    }

    /**
     * Format an array value
     *
     * @param array $value
     * @param $operator
     * @return array|string
     */
    protected function formatArrayValue(array $value, $operator)
    {
        // If a value with commas was exploded, then
        // we need to put it back together
        if (! in_array($operator, ['IN', 'NOT IN'])) {
            return implode(', ', $value);
        }

        return array_map(
            function ($v) use ($operator) {
                return $this->formatValue($v, $operator);
            },
            $value
        );
    }
}

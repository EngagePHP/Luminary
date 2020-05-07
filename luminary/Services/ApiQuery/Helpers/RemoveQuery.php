<?php

namespace Luminary\Services\ApiQuery\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RemoveQuery
{
    protected $builder;

    protected $model;

    protected $query;

    protected $wheres;

    public function __construct(Builder $builder, Model $model, string $type)
    {
        $this->builder = $builder;
        $this->model = $model;
        $this->type = $type;
        $this->query = $builder->getQuery();
        $this->wheres = $this->query->wheres;
    }

    public static function run(Builder $builder, Model $model, string $type)
    {
        $instance = new static($builder, $model, $type);
        $wheres = $instance->wheres;
        $instance->columns($type, $model)
            ->each(function($find) use(&$wheres, $instance) {
                $wheres = $instance->filterWheres($wheres, $find);
            });

        $instance->query->wheres = $wheres;
    }

    protected function filterWheres(array $wheres, array $find)
    {
        return collect($wheres)->filter(function($where) use($find) {
            return $where !== $find;
        })->all();
    }

    protected function columns()
    {
        $column = null;
        $columnQualified = null;

        switch($this->type) {
            case 'trashed':
                $column = $this->model->getDeletedAtColumn();
                $columnQualified = $this->model->getQualifiedDeletedAtColumn();
                break;
            case 'archived':
                $column = $this->model->getArchivedAtColumn();
                $columnQualified = $this->model->getQualifiedArchivedAtColumn();
                break;
            case 'expired':
                $column = $this->model->getExpiredAtColumn();
                $columnQualified = $this->model->getQualifiedExpiredAtColumn();
                break;
        }

        return collect(compact('column','columnQualified'))
            ->filter()
            ->transform(function($column) {
                return [
                    'type' => 'Null',
                    'column' => $column,
                    'boolean' => 'and'
                ];
            });
    }
}

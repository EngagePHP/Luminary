<?php

namespace Luminary\Services\ApiSoftDeletes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Collection;

class ApiEventSubscriber {

    /**
     * Check if Model is using the restore method
     *
     * [Class = [<model_id>]]
     *
     * @var \Illuminate\Support\Collection
     */
    protected static $softDeleteRestores;

    /**
     * Check if the Model is currently restoring
     * using the ApiEventSubscriber
     *
     * [Class = [<model_id>]]
     *
     * @var \Illuminate\Support\Collection
     */
    protected static $apiRestores;

    /**
     * Set the model as Soft Delete Restore
     * if not triggered by an api restore
     *
     * @return void
     */
    public function restoring(string $event, $data)
    {
        $class = $this->getModelClass($data);
        $id = $this->getModelId($data);

        if(!$this->hasApiRestore($class, $id)) {
            $this->addSoftDeleteRestore($class, $id);
        }
    }

    /**
     * On Model save Check if restoring through the API
     * and set the Model as an API restore
     *
     * @param string $event
     * @param $data
     * @return bool
     */
    public function saving(string $event, $data)
    {
        $class = $this->getModelClass($data);
        $model = $this->getModel($data);
        $id = $this->getModelId($data);

        if(!is_null($id) && !$this->hasSoftDeleteRestore($class, $id) && $this->isRestoring($model)) {
            $this->addApiRestore($class, $id, true);
            $event = event('eloquent.restoring: ' . $class, $model);
            $result = $this->parseRestoringEventResult($event);
            $this->addApiRestore($class, $id, $result);

            return $result;
        }
    }

    /**
     * Trigger Restored event if saving is
     * an API Restore
     *
     * @param string $event
     * @param $data
     */
    public function saved(string $event, $data)
    {
        $class = $this->getModelClass($data);
        $id = $this->getModelId($data);

        if($this->hasApiRestore($class, $id) && $this->getApiRestore($class, $id) !== false) {
            $model = $this->getModel($data);
            event('eloquent.restored: ' . $class, $model);
        }
    }

    /**
     * Subscribe to eloquent events to determine
     * Model restores
     *
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen('eloquent.restoring: *', static::class . '@restoring');
        $events->listen('eloquent.saving: *', static::class . '@saving');
        $events->listen('eloquent.saved: *', static::class . '@saved');
    }

    /**
     * Get the model instance from the event data
     *
     * @param array $data
     * @return Model
     */
    protected function getModel(array $data): Model
    {
        return array_get($data, 'model') ?: array_first($data);
    }

    /**
     * Get the model Class name and namespace
     *
     * @param array $data
     * @return string
     */
    protected function getModelClass(array $data): string
    {
        $model = $this->getModel($data);
        return get_class($model);
    }

    /**
     * Get the model id
     *
     * @param array $data
     * @return int|null
     */
    protected function getModelId(array $data)
    {
        return $this->getModel($data)->id;
    }

    /**
     * Get the soft delete restores for all
     * or by individual class namespace
     *
     * @param string|null $class
     * @return Collection|array
     */
    protected function getSoftDeleteRestores(string $class = null)
    {
        if(!static::$softDeleteRestores instanceof Collection) {
            static::$softDeleteRestores = new Collection;
        }

        return $class ? static::$softDeleteRestores->get($class, []) : static::$softDeleteRestores;
    }

    /**
     * Get the api restores for all
     * or by individual class namespace
     *
     * @param string|null $class
     * @return Collection|array
     */
    protected function getApiRestores(string $class = null)
    {
        if(!static::$apiRestores instanceof Collection) {
            static::$apiRestores = new Collection;
        }

        return $class ? static::$apiRestores->get($class, []) : static::$apiRestores;
    }

    /**
     * Get an individual class restore by model id
     *
     * @param string $class
     * @param int $id
     * @return mixed
     */
    protected function getApiRestore(string $class, int $id)
    {
        $restores = $this->getApiRestores($class);
        return array_get($restores, $id);
    }

    /**
     * Add a model to the Soft Delete Restore collection
     *
     * @param string $class
     * @param int $id
     */
    protected function addSoftDeleteRestore(string $class, int $id)
    {
        $restores = $this->getSoftDeleteRestores($class);
        $restores[] = $id;

        static::$softDeleteRestores->put($class, array_unique($restores));
    }

    /**
     * Add a model to the Api Restore collection
     *
     * @param string $class
     * @param int $id
     * @param $result
     */
    protected function addApiRestore(string $class, int $id, $result)
    {
        $restores = $this->getApiRestores($class);
        $restores[$id] = $result;

        static::$apiRestores->put($class, $restores);
    }

    /**
     * Parse restoring event to determine if
     * false or true on save
     *
     * @param $eventResult
     * @return bool
     */
    protected function parseRestoringEventResult($eventResult)
    {
        if(is_array($eventResult)) {
            return !empty($eventResult);
        }

        return $eventResult;
    }

    /**
     * Check if a model is set for a soft
     * delete restore
     *
     * @param string $class
     * @param int|null $id
     * @return bool
     */
    protected function hasSoftDeleteRestore(string $class, int $id = null): bool
    {
        $restores = $this->getSoftDeleteRestores($class);
        return !is_null($id) && array_search($id, $restores) !== false;
    }

    /**
     * Check if a model is set for a api restore
     *
     * @param string $class
     * @param int|null $id
     * @return bool
     */
    protected function hasApiRestore(string $class, int $id = null): bool
    {
        return !is_null($id) && !is_null($this->getApiRestore($class, $id));
    }

    /**
     * Check if the model being save is
     * actually being restored
     *
     * @param Model $model
     * @return bool
     */
    protected function isRestoring(Model $model): bool
    {
        if(!method_exists($model, 'getDeletedAtColumn')) {
            return false;
        }

        $deletedAtColumn = $model->getDeletedAtColumn();

        if(!$model->isDirty($deletedAtColumn)) {
            return false;
        }

        $original = $model->getOriginal($deletedAtColumn);
        $updated = $model->{$deletedAtColumn};

        return !is_null($original) && is_null($updated);
    }
}
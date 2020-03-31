<?php

namespace Luminary\Models\Archive;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait ArchiveModelTrait
{
    /**
     * Bool to trigger the
     * archived model event
     *
     * @var bool
     */
    public $archived = false;

    /**
     * Bool to trigger the
     * unarchived model event
     *
     * @var bool
     */
    public $unarchived = false;

    /**
     * Set the archived observer
     *
     * @return void
     */
    public static function bootArchiveModelTrait()
    {
        static::observe(ArchiveObserver::class);
        static::setModelArchivedScope();
    }

    /**
     * Scope the relationship attributes as part of the Model
     *
     * @return void
     */
    protected static function setModelArchivedScope() :void
    {
        static::addGlobalScope(new ArchiveModelScope);
    }

    /**
     * Register a archiving model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function archiving($callback)
    {
        static::registerModelEvent('archiving', $callback);
    }

    /**
     * Register a archived model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function archived($callback)
    {
        static::registerModelEvent('archived', $callback);
    }

    /**
     * Register a archiving model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function unarchiving($callback)
    {
        static::registerModelEvent('unarchiving', $callback);
    }

    /**
     * Register a archived model event with the dispatcher.
     *
     * @param  \Closure|string  $callback
     * @return void
     */
    public static function unarchived($callback)
    {
        static::registerModelEvent('unarchived', $callback);
    }

    /**
     * Get the name of the "archived at" column.
     *
     * @return string
     */
    public function getArchivedAtColumn()
    {
        return defined('static::ARCHIVED_AT') ? static::ARCHIVED_AT : 'archived_at';
    }

    /**
     * Get the fully qualified "archived at" column.
     *
     * @return string
     */
    public function getQualifiedArchivedAtColumn()
    {
        return $this->qualifyColumn($this->getArchivedAtColumn());
    }

    /**
     * Trigger the archived event
     *
     * @return void
     */
    public function triggerArchiving()
    {
        if ($this->fireModelEvent('archiving') !== false) {
            $this->archived = true;
        }
    }

    /**
     * Trigger the unarchived event
     *
     * @return void
     */
    public function triggerUnarchiving()
    {
        if ($this->fireModelEvent('unarchiving') !== false) {
            $this->unarchived = true;
        }
    }

    /**
     * Trigger the archived event
     *
     * @return void
     */
    public function triggerArchived()
    {
        if ($this->fireModelEvent('archived') !== false) {
            $this->archived = true;
        }
    }

    /**
     * Trigger the unarchived event
     *
     * @return void
     */
    public function triggerUnarchived()
    {
        if ($this->fireModelEvent('unarchived') !== false) {
            $this->unarchived = true;
        }
    }
}
<?php

namespace Luminary\Services\ApiQuery\Testing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Luminary\Services\ApiQuery\QueryTrait;

class Location extends Model
{
    use SoftDeletes;
    use QueryTrait;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'locations';

    /**
     * Fillable fields for a Content instance.
     *
     * @var array
     */
    protected $fillable = ['*'];

    /**
     * Fields that should stay hidden from response.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Users Relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

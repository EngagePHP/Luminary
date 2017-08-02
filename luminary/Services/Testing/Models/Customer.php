<?php

namespace Luminary\Services\Testing\Models;

use Luminary\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Luminary\Services\Tenants\ByTenant;

class Customer extends Model
{
    use ByTenant;
    use SoftDeletes;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * Fillable fields for a Content instance.
     *
     * @var array
     */
    protected $fillable = [];

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

    /**
     * The location relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}

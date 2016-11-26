<?php

namespace Codecourse\Taggy\Scopes;

trait TagOrderableScopesTrait
{
    /**
     * A scope to retrieve any tags where the count is
     * greater than or equal to the given value.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  integer $count
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedGte($query, $count)
    {
        return $query->where('count', '>=', $count);
    }

    /**
     * A scope to retrieve any tags where the count is
     * greater than the given value.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  integer $count
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedGt($query, $count)
    {
        return $query->where('count', '>', $count);
    }

    /**
     * A scope to retrieve any tags where the count is
     * less than or equal to the given value.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  integer $count
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedLte($query, $count)
    {
        return $query->where('count', '<=', $count);
    }

    /**
     * A scope to retrieve any tags where the count is
     * less than the given value.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  integer $count
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeUsedLt($query, $count)
    {
        return $query->where('count', '<', $count);
    }
}

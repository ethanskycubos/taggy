<?php

namespace Codecourse\Taggy\Scopes;

trait TaggableScopesTrait
{
    /**
     * A scope to retrieve any model that has ANY OF the given tags.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array  $tags
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnyTag($query, array $tags)
    {
        return $query->hasTags($tags);
    }

    /**
     * A scope to retrieve any model that has ALL the given tags.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array  $tags
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllTags($query, array $tags)
    {
        foreach ($tags as $tag) {
            $query->hasTags([$tag]);
        }

        return $query;
    }

    /**
     * A scope to retrieve any model that has the given tags.
     *
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array  $tags
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasTags($query, array $tags)
    {
        return $query->whereHas('tags', function ($query) use ($tags) {
            return $query->whereIn('slug', $tags);
        });
    }
}

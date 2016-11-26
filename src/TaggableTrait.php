<?php

namespace Codecourse\Taggy;

use Codecourse\Taggy\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Codecourse\Taggy\Scopes\TaggableScopesTrait;

trait TaggableTrait
{
    use TaggableScopesTrait;

    /**
     * The tags relationship to this model.
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Tag this model.
     *
     * @param  mixed $tags
     * @return void
     */
    public function tag($tags)
    {
        $this->addTags($this->getWorkableTags($tags));
    }

    /**
     * Delete all tags and retag model with given tags.
     *
     * @param  mixed $tags
     * @return void
     */
    public function retag($tags)
    {
        $this->removeAllTags();

        $this->tag($tags);
    }

    /**
     * Remove either all tags or given tags from this model.
     *
     * @param  mixed $tags
     * @return void
     */
    public function untag($tags = null)
    {
        if ($tags === null) {
            $this->removeAllTags();
            return;
        }

        $this->removeTags($this->getWorkableTags($tags));
    }

    /**
     * Removes all tags.
     *
     * @return void
     */
    private function removeAllTags()
    {
        $this->removeTags($this->tags);
    }

    /**
     * Remove specified tags.
     *
     * @param  Illuminate\Support\Collection $tags
     * @return void
     */
    private function removeTags(Collection $tags)
    {
        $this->tags()->detach($tags);

        foreach ($tags->where('count', '>', 0) as $tag) {
            $tag->decrement('count');
        }
    }

    /**
     * Adds specified tags.
     *
     * @param  Illuminate\Support\Collection $tags
     * @return void
     */
    private function addTags(Collection $tags)
    {
        $sync = $this->tags()->syncWithoutDetaching($tags->pluck('id')->toArray());

        foreach (array_get($sync, 'attached') as $attachedId) {
            $tags->where('id', $attachedId)->first()->increment('count');
        }
    }

    /**
     * Get a collection of Tag models by slug.
     *
     * @param  array  $tags
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function getTagModels(array $tags)
    {
        return Tag::whereIn('slug', $this->normaliseTagNames($tags))->get();
    }

    /**
     * Given either an array of Tag slugs, a single Tag model
     * or a Collection of Tags, return only a Collection of
     * Tags so they can be easily worked with in here.
     *
     * @param  mixed $tags
     * @return Illuminate\Database\Eloquent\Collection
     */
    private function getWorkableTags($tags)
    {
        if (is_array($tags)) {
            return $this->getTagModels($tags);
        }

        if ($tags instanceof Model) {
            return $this->getTagModels([$tags->slug]);
        }

        // Assume a collection
        return $this->filterTagsCollection($tags);
    }

    /**
     * Filter Tags in a Collection to make sure they are actually
     * an instance of a model. We're not checking for a Tag
     * model here, because another model name may be used.
     *
     * @param  Illuminate\Support\Collection $tags
     * @return Illuminate\Support\Collection
     */
    private function filterTagsCollection(Collection $tags)
    {
        return $tags->filter(function ($tag) {
            return $tag instanceof Model;
        });
    }

    /**
     * Normalise tag names provided when querying (perhaps to tag or
     * untag a model), such that they can always be used to lookup
     * tags from the database by their slug.
     *
     * @param  array  $tags
     * @return array
     */
    private function normaliseTagNames(array $tags)
    {
        return array_map(function ($tag) {
            return str_slug($tag);
        }, $tags);
    }
}

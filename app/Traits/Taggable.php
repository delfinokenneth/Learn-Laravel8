<?php

namespace App\Traits;
namespace App\Models;

trait Taggable {

    protected static function bootTaggable()
    {
        static::updating(function ($model)
        {

        });

        static::created(function ($model)
        {

        });
    }

    public function tags()
    {
        // return $this->BelongsToMany(Tag::class)->withTimestamps();
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    private static function findTagsInContent($content)
    {
        preg_match_all('/@([^@]+)@/m', $content, $tags);

        return Tag::whereIn('name', $tags[1] ?? [])->get();
    }

}
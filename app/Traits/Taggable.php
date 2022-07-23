<?php

namespace App\Traits;
namespace App\Models;

trait Taggable {

    public function tags()
    {
        // return $this->BelongsToMany(Tag::class)->withTimestamps();
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public function blogPosts()
    {
        // return $this->belongsToMany(BlogPost::class)->withTimestamps()->as('tagged');
        return $this->morphedByMany('App\Models\BlogPost', 'taggable')->withTimeStamps()->as('tagged');
    }

    public function comments()
    {
        return $this->morphedByMany('App\Models\Comment', 'taggable')->withTimestamps()->as('tagged');
    }

}

<?php

namespace App\Models;
use App\Traits\Taggable;

use App\Scopes\LatestScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class Comment extends Model
{
    use HasFactory;

    use SoftDeletes, Taggable;

    protected $fillable = ['user_id', 'content'];

    public function commentable()
    {
        return $this->morphTo();
    }
    // // blog_post_id 
    // public function blogPost()
    // {
    //     //return $this->belongsTo('App\BlogPost', 'post_id', 'blog_post_id');
    //     //return $this->belongsTo(BlogPost::class, 'post_id');
    //     //return $this->belongsTo('App\Models\BlogPost', 'post_id');
    //     return $this->belongsTo('App\Models\BlogPost');
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function scopeLatest(Builder $query)
    {
        return $query->orderBy(static::CREATED_AT, 'desc');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function (Comment $comment) {
            // dump($comment);
            // dd(BlogPost::class);

            if($comment->commentable_type === BlogPost::class);
            Cache::tags(['blog-post'])->forget("blog-post-{$comment->commentable_id}");
            Cache::tags(['blog-post'])->forget("mostCommented");
        });

        // static::addGlobalScope(new LatestScope);
    }
}

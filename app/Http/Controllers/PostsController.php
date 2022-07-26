<?php

namespace App\Http\Controllers;


use App\Http\Requests\StorePost;

use Illuminate\Http\Request;
//use Illuminate\Http\StorePost;

use App\Models\BlogPost;
use App\Models\Image;


use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth')
            ->only(['create', 'store', 'edit', 'update', 'destory']);

    }

    public function index()
    {
        // dd(BlogPost::class->latestWithRelations()->get());

        return view(
            'posts.index', 
            [
                'posts' => BlogPost::latestWithRelations()->get(),
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePost $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = $request->user()->id;
        $blogPost = Models\BlogPost::create($validatedData);

        if($request->hasFile('thumbnail'))
        {
            $path = $request->file('thumbnail')->store('thumbnails');
            $blogPost->image()->save(
                Image::make(['path' => $path]),
            );
        }
    

        $request->session()->flash('status','The blog post was created!');
        return redirect()->route('posts.show', ['post' => $blogPost->id]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // abort_if(!isset($this->posts[$id]), 404);
        // return view('posts.show', 
        //     ['post' => BlogPost::with(['comments' => function ($query) {
        //         return $query->latest();
        //     }])->findOrFail($id)
        // ]);
        $blogPost = Cache::tags(['blog-post'])->remember("blog-post-{$id}", 60, function() use($id) {
            return Models\BlogPost::with('comments', 'tags', 'user', 'comments.user')
                ->findOrFail($id);
        });

        $sessionId = session()->getId();
        $counterKey = "blog-post-{$id}-counter";
        $usersKey = "blog-post-{$id}-users";

        $users = Cache::tags(['blog-post'])->get($usersKey, []);
        $usersUpdate = [];
        $difference = 0;
        $now = now();

        foreach ($users as $session => $lastVisit)
        {
            if ($now->diffInMinutes($lastVisit) >= 1)
            {
                $difference--;
            }
            else
            {
                $userUpdate[$session] = $lastVisit;
            }
        }

        if( !array_key_exists($sessionId, $users)|| $now->diffInMinutes($users[$sessionId]) >= 1)
        {
            
            $difference++;
        }

        $usersUpdate[$sessionId] = $now;
        Cache::tags(['blog-post'])->forever($usersKey, $usersUpdate);

        if (!Cache::tags(['blog-post'])->has($counterKey))
        {   
            Cache::tags(['blog-post'])->forever($counterKey, 1);
        }
        else
        {
            Cache::tags(['blog-post'])->increment($counterKey, $difference);
        }


        $counter = Cache::tags(['blog-post'])->get($counterKey);

        return view('posts.show', [
            'post' => $blogPost,
            'counter' => $counter,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Models\BlogPost::findOrFail($id);
        $this->authorize('update', $post);
        return view('posts.edit', ['post' => Models\BlogPost::findOrFail($id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(StorePost $request, $id)
    {
        $post = Models\BlogPost::findOrFail($id);
        // if(Gate::denies('update-post', $post))
        //     abort(403, "You can't edit this blogpost");
        //$this->authorize('update', $post);
        $this->authorize($post);

        $validatedData = $request->validated();
        $post->fill($validatedData);

        if($request->hasFile('thumbnail'))
        {
            $path = $request->file('thumbnail')->store('thumbnails');

            if ($post->image)
            {
                Storage::delete($post->image->path);
                $post->image->path = $path;
                $post->image->save();
            }
            else
            {
                $post->image()->save(
                    Image::make(['path' => $path]),
                );
            }

        }

        $post->save();

        $request->session()->flash('status', 'Blog post was updated!');

        return redirect()->route('posts.show', ['post' => $post->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $post = Models\BlogPost::findOrFail($id);
        //$this->authorize('delete', $post);
        $this->authorize($post);
        $post->delete();

        $request->session()->flash('status', 'Blog post was deleted!');

        return redirect()->route('posts.index');
    }
}

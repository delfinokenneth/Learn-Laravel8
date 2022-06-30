<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePost;
//use Illuminate\Http\StorePost;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
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
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;
        $blogPost = BlogPost::create($validated);
        // $post = new BlogPost();
        // $post->title = $validated['title'];
        // $post->content = $validated['content'];
        // $post->save();
        
        $hasFile = $request->hasFile('thumbnail');

        dump($hasFile);
        
        if($hasFile)
        {
            $file = $request->file('thumbnail');
            dump($file);
            dump($file->getClientMimeType());
            dump($file->getClientOriginalExtension());

            // dump($file->store('thumbnail'));
            // dump(Storage::disc('public')->putFile('thumbnails', $file));

            $name1 = $file->storeAs('thumbnails', $blogPost->id . '.' . $file->getExtension());
            $name2 = Storage::putFileAs('thumbnails', $file, $blogPost->id . '.' . $file->guessExtension());
            
            dump(Storage::url($name1));
            dump(Storage::disk('local'));
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
            return BlogPost::with('comments', 'tags', 'user', 'comments.user')
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
        $post = BlogPost::findOrFail($id);
        $this->authorize('update', $post);
        return view('posts.edit', ['post' => BlogPost::findOrFail($id)]);
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
        $post = BlogPost::findOrFail($id);
        // if(Gate::denies('update-post', $post))
        //     abort(403, "You can't edit this blogpost");
        $this->authorize('update', $post);

        $validated = $request->validated();
        $post->fill($validated);
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
    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);
        $this->authorize('delete', $post);
        $post->delete();

        session()->flash('status', 'Blog post was deleted!');

        return redirect()->route('posts.index');
    }
}

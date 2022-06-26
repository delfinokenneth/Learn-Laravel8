@extends('layouts.app')


@section('title', $post->title)

@section('content')
<div class="row">
    <div class="col-8">

    <h1>  
        {{ $post->title }}
            @component('components.badge', ['show' => now()->diffInMinutes($post->created_at) < 5]) 
                Brand new Post! 
            @endcomponent 
    </h1>
    <p> {{ $post->content }}</p>

    {{-- <p class="text-muted"> 
        Added {{ $post->created_at->diffForHumans() }}
    </p> --}}


    @component('components.updated', ['date' => $post->created_at, 'name' => $post->user->name]) 
    @endcomponent
    @component('components.updated', ['date' => $post->updated_at]) 
        Updated
    @endcomponent

    @component('components.tags', ['tags' => $post->tags])
    @endcomponent

    <p> Currently read by {{ $counter }} people </p>

    <h4> Comments </h4>

    @include('comments._form')

    @forelse($post->comments as $comment)
        <p>
            {{  $comment->content }}
        </p>

        {{-- {{ dd($comment->user->name) }} --}}

        @component('components.updated', ['date' => $comment->created_at, 'name' => $comment->user->name]) 
        @endcomponent


        {{-- <p class="text-muted">
            added {{  $comment->created_at->diffForHumans()  }}
        </p> --}}
        @empty
            <p> No Comments yet! </p>
        @endforelse

    </div>
    <div class="col-4">
        @include('posts._activity')
    </div>


@endsection



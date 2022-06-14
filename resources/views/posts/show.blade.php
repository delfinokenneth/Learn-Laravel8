@extends('layouts.app')


@section('title', $post->title)

@section('content')
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

<h4> Comments </h4>

@forelse($post->comments as $comment)
    <p>
        {{  $comment->content }}
    </p>

    @component('components.updated', ['date' => $comment->created_at]) 
    @endcomponent

    {{-- <p class="text-muted">
        added {{  $comment->created_at->diffForHumans()  }}
    </p> --}}
@empty
    <p> No Comments yet! </p>
@endforelse



@endsection



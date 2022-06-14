@extends('layouts.app')


@section('title', 'Blog Posts')
        
@section('content')
{{-- @each('posts.partials.post', $posts, 'post') --}}
@forelse($posts as $key => $post)
    @include('posts.partials.post')
@empty
No posts found!
@endforelse

<div class="col-4">
    <div class="container">
        <div class="row mt-4">
            @card(['title' => 'Most Commented'])
                @slot('subtitle')
                    What people are currently talking about
                @endslot
                @slot('items', collect($mostCommented->pluck('title')))
            @endcard
        </div>

        <div class="row mt-4">
            @card(['title' => 'Most Active'])
                @slot('subtitle')
                    Users with most posts written
                @endslot
                @slot('items', collect($mostActive->pluck('name')))
            @endcard
        </div>

        <div class="row mt-4">
            @card(['title' => 'Most Active Last Month'])
                @slot('subtitle')
                    Users with most posts written in the month
                @endslot
                @slot('items', collect($mostActiveLastMonth->pluck('name')))
            @endcard
        </div>
    </div>
</div>

@endsection
<h3>
    @if($post->trashed())
        <del>
    @endif
    <a class="{{ $post->trashed() ? 'text-muted' : '' }}"
        href="{{ route('posts.show', ['post' => $post->id]) }}">{{ $post->title }}</a>
    @if($post->trashed())
        </del>
    @endif
</h3>


{{-- <p class="text-muted"> 
    Added {{ $post->created_at->diffForHumans() }}
    by {{  $post->user->name }}
</p> --}}

@component('components.updated', ['date' => $post->created_at, 'name' => $post->user->name]) 

@endcomponent

@component('components.tags', ['tags' => $post->tags])
@endcomponent



<div class="mb-3">
    <a href="{{ route('posts.show', ['post' => $post->id]) }}" class="btn btn-primary"> View </a>

    @auth
        @can('update', $post)
        <a href="{{ route('posts.edit', ['post' => $post->id]) }}" class="btn btn-primary"> Edit </a>
        @endcan
    @endauth

    @if($post->comments_count)
    <p>{{ $post->comments_count }} comments</p>
    
    @else
    <p>No comments yet!</p>
    @endif

    @cannot('delete', $post)
    <p> You can't delete this post </p>
    @endcannot

    @auth
        @if(!$post->trashed())
            @can('delete', $post)
                <form method="POST" class="fm-inline"
                    action="{{ route('posts.destroy', ['post' => $post->id]) }}">
                    @csrf
                    @method('DELETE')
                    <input type="submit" value="Delete!" class="btn btn-primary"/>
                </form>
            @endcan
        @endif
    @endauth
</div> 

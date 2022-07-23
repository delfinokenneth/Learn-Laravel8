@forelse($comments as $comment)
<p>
    {{  $comment->content }}
</p>
@component('components.updated', ['date' => $comment->created_at, 'name' => $comment->user->name, 'userId' => $comment->user->id]) 
@endcomponent

{{-- <p class="text-muted">
    added {{  $comment->created_at->diffForHumans()  }}
</p> --}}
@empty
    <p> No Comments yet! </p>
@endforelse
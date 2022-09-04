@component('mail::message')
# Comment was posted on your blogpost

Hi {{  $comment->commentable->user->name }}

Someone has commented on your blogpost

@component('mail::button', ['url' => route('posts.show', ['post' => $comment->commentable->id])])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent

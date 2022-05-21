<h3> {{ $post->title }}</h3>

<div class="mb-3">
    <a href="{{ route('posts.show', ['post' => $post->id]) }}" class="btn btn-primary"> View </a>
    <a href="{{ route('posts.edit', ['post' => $post->id]) }}" class="btn btn-primary"> Edit </a>
    <form class="d-inline" action="{{  route('posts.destroy', ['post' => $post->id]) }}" method="POST">
        @csrf
        @method('DELETE')
        <input type="submit" value="DELETE" class="btn btn-primary">
    </form>
</div>
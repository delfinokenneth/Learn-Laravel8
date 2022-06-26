<div class="mb-2 mt-2">

@auth
    

<form method="POST" action="#"> 
    @csrf

    <div class="form-group">
        <textarea type="text" name="content" class="form-control"> </textarea>
    </div>

    <div> <input type="submit" class="btn btn-primary btn-block"> Add comment </div>
</form>
@else
    <a href="{{  route('login') }}"> Sign-in </a> to post comments!
@endauth
</div>
<hr/>
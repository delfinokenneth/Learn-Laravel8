@extends('layouts.app')

@section('title', 'Contact Page')

@section('content')
    <h1>Contact Page</h1>
    <p> Hello this is contact! </p>

    @can('home.secret')
        <p> 
            <a href="{{  route('secret') }}">
                Special contact details 
            </a>
        </p>
    @endcan
@endsection
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1>Welcome to the Dashboard</h1>

    @auth
        <p>Hello, {{ auth()->user()->name }}!</p>
        <p>This is your private dashboard.</p>
    @endauth
@endsection

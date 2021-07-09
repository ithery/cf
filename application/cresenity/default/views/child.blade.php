<!-- Stored in resources/views/child.blade.php -->

@extends('layouts.master')

@section('title', 'Testing Page')

@section('sidebar')

    <p>This is prepended to the master sidebar.</p>
    
    @parent

    <p>This is appended to the master sidebar.</p>
@endsection

@section('content')
    <p>This is my body content.</p>
@endsection
@extends('layouts.admin')

@section('title', 'Add IP Record')

@section('content')
    @include('admin.ip._form', ['action' => route('admin.ip.store'), 'method' => 'POST'])
@endsection

@extends('layouts.admin')

@section('title', 'Edit IP Record')

@section('content')
    @include('admin.ip._form', ['action' => route('admin.ip.update', $ip->id), 'method' => 'PUT'])
@endsection

@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
    @include('admin.products._form', ['action' => route('admin.products.update', $product), 'method' => 'PUT'])
@endsection

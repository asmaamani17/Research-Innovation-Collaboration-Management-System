@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
    @include('admin.products._form', ['action' => route('admin.products.store'), 'method' => 'POST'])
@endsection

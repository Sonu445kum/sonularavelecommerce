@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Admin Dashboard</h1>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card text-center bg-primary text-white shadow">
                <div class="card-body">
                    <h5>Total Products</h5>
                    <h3>{{ $productCount ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-center bg-success text-white shadow">
                <div class="card-body">
                    <h5>Total Orders</h5>
                    <h3>{{ $orderCount ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-center bg-warning text-white shadow">
                <div class="card-body">
                    <h5>Total Categories</h5>
                    <h3>{{ $categoryCount ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-center bg-danger text-white shadow">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <h3>{{ $userCount ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

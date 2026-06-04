@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="bg-white border rounded-2 p-4 p-md-5">
                <p class="text-uppercase text-secondary small fw-semibold mb-2">Spec-driven Laravel foundation</p>
                <h1 class="h2 mb-3">{{ config('app.name', 'Values01') }}</h1>
                <p class="lead mb-4">
                    Laravel 13, Livewire 4, and Bootstrap 5 are ready for feature specs, acceptance criteria, and implementation.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge text-bg-primary">Laravel 13</span>
                    <span class="badge text-bg-success">Livewire 4</span>
                    <span class="badge text-bg-dark">Bootstrap 5</span>
                    <span class="badge text-bg-secondary">Specs first</span>
                </div>
            </div>
        </div>
    </div>
@endsection

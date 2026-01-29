@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div id="alert-success"
            class="mb-6 flex items-center rounded-xl border border-green-200 bg-green-50 p-4 text-green-800 shadow-sm dark:border-green-900/30 dark:bg-green-900/20 dark:text-green-400">
            <i class="bi bi-check-circle-fill mr-3 text-xl"></i>
            <div class="text-sm font-bold">
                {{ session('success') }}
            </div>
            <button type="button" onclick="document.getElementById('alert-success').remove()"
                class="ml-auto text-green-600 hover:text-green-800">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div id="alert-error"
            class="mb-6 flex items-center rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm dark:border-red-900/30 dark:bg-red-900/20 dark:text-red-400">
            <i class="bi bi-exclamation-triangle-fill mr-3 text-xl"></i>
            <div class="text-sm font-bold">
                {{ session('error') ?? 'Please correct the highlighted errors below.' }}
            </div>
            <button type="button" onclick="document.getElementById('alert-error').remove()"
                class="ml-auto text-red-600 hover:text-red-800">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Create New Organization</h5>
                            <a href="{{ route('organizations.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('organizations.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Organization Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}"
                                    placeholder="Enter organization name" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Organization names must be unique.</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Organization
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('title', 'Edit department')
@section('content')
    <div class="card" style="max-width:480px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Edit department</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.departments.update', $department) }}">
                @csrf @method('PUT')
                @include('admin.departments._form')
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

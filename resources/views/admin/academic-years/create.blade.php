@extends('layouts.app')
@section('title', 'Add academic year')
@section('content')
    <div class="card" style="max-width:480px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Add academic year</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.academic-years.store') }}">
                @csrf
                @include('admin.academic-years._form')
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-primary">Save academic year</button>
                    <a href="{{ route('admin.academic-years.index') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

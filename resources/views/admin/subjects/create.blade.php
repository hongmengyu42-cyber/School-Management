@extends('layouts.app')
@section('title', 'Add subject')
@section('content')
    <div class="card" style="max-width:560px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Add subject</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.subjects.store') }}">
                @csrf
                @include('admin.subjects._form')
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-primary">Save subject</button>
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

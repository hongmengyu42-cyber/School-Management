@extends('layouts.app')
@section('title', 'Edit subject')
@section('content')
    <div class="card" style="max-width:560px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Edit subject</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.subjects.update', $subject) }}">
                @csrf @method('PUT')
                @include('admin.subjects._form')
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

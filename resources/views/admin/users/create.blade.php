@extends('layouts.app')
@section('title', 'Add user')
@section('content')
    <div class="card" style="max-width:520px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Add user</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                @include('admin.users._form')
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-primary">Save user</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

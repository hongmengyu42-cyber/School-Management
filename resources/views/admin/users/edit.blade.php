@extends('layouts.app')
@section('title', 'Edit user')
@section('content')
    <div class="card" style="max-width:520px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Edit user</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf @method('PUT')
                @include('admin.users._form')
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

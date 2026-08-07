@extends('layouts.app')
@section('title', 'Add semester')
@section('content')
    <div class="card" style="max-width:520px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Add semester</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.semesters.store') }}">
                @csrf
                @include('admin.semesters._form')
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-primary">Save semester</button>
                    <a href="{{ route('admin.semesters.index') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

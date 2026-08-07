@if (session('status'))
    <div class="flash">{{ session('status') }}</div>
@endif

@if (session('import_errors') && count(session('import_errors')))
    <div class="flash flash-error">
        <strong>Some rows failed to import:</strong>
        <ul style="margin:6px 0 0; padding-left:18px;">
            @foreach (session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($errors->any())
    <div class="flash flash-error">
        <strong>Please fix the following:</strong>
        <ul style="margin:6px 0 0; padding-left:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

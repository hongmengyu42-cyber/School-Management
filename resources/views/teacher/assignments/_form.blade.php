<div class="field">
    <label for="title">Title</label>
    <input id="title" type="text" name="title" value="{{ old('title', $assignment->title ?? '') }}" required>
</div>
<div class="field">
    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4">{{ old('description', $assignment->description ?? '') }}</textarea>
</div>
<div class="field">
    <label for="due_date">Due date</label>
    <input id="due_date" type="datetime-local" name="due_date" value="{{ old('due_date', optional($assignment->due_date ?? null)->format('Y-m-d\TH:i')) }}">
</div>
<div class="field">
    <label for="max_points">Max points</label>
    <input id="max_points" type="number" min="1" name="max_points" value="{{ old('max_points', $assignment->max_points ?? 100) }}" required>
</div>

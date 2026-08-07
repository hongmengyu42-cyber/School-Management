<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('quiz')->subject);
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('choices') && is_string($this->input('choices'))) {
            $decoded = json_decode($this->input('choices'), true);
            $this->merge([ 'choices' => is_array($decoded) ? $decoded : null ]);
        }

        if (! $this->filled('choices') && $this->filled('choices_raw')) {
            $choices = collect(explode("\n", $this->input('choices_raw')))
                ->map(fn ($line) => trim($line))
                ->filter()
                ->values()
                ->all();

            $this->merge([ 'choices' => $choices ]);
        }
    }

    public function rules(): array
    {
        return [
            'question_text' => ['required', 'string'],
            'choices' => ['nullable', 'array'],
            'choices.*' => ['string'],
            'correct_answer' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'sender_type' => 'required|in:1,2',
            'is_schedule' => 'required|boolean',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'required|exists:users,id',
            'published_at' => ['excludeIf:is_schedule,0', 'required', 'date_format:Y/m/d H:i', 'after:now'],
            'published_end_at' => [
                'nullable', 'date_format:Y/m/d H:i',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $publishedAt = Carbon::parse($this->published_at);
                        $publishedEndAt = Carbon::parse($value);

                        if ($publishedAt >=  $publishedEndAt) {
                            $fail(__('The end time must be greater than the start time or current time.'));
                        }
                    }
                }
            ],
        ];
    }
}

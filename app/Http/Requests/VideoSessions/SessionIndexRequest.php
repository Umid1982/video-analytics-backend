<?php

namespace App\Http\Requests\VideoSessions;

use Illuminate\Foundation\Http\FormRequest;

class SessionIndexRequest extends FormRequest
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
            'status' => ['nullable', 'in:pending,processing,completed,failed,started'],
            'source_type' => ['nullable', 'in:file,url,stream'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'order_by' => ['nullable', 'in:id,status,source_type,created_at,total_people'],
            'order_dir' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100']
        ];
    }

    public function filters(): array
    {
        return array_merge(
            ['user_id' => $this->user()->id],
            array_filter($this->only([
                'status', 'source_type', 'date_from', 'date_to', 'order_by', 'order_dir', 'per_page'
            ]))
        );
    }
}

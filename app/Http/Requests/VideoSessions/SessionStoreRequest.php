<?php declare(strict_types=1);

namespace App\Http\Requests\VideoSessions;

use App\DTOs\SessionDTO;
use Illuminate\Foundation\Http\FormRequest;

class SessionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_type' => 'required|string|in:file,url,stream',
            'source_path' => 'required|string',
            'duration' => 'nullable|integer|min:1',
            'confidence_threshold' => 'nullable|numeric|min:0|max:1',
        ];
    }

    public function toDTO(): SessionDTO
    {
        return new SessionDTO(
            userId: $this->user()->id,
            sourceType: $this->input('source_type'),
            sourcePath: $this->input('source_path'),
            duration: $this->input('duration'),
            confidenceThreshold: $this->input('confidence_threshold', 0.5),
        );
    }
}


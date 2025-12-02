<?php declare(strict_types=1);

namespace App\Http\Requests\Reports;

use App\DTOs\ReportDTO;
use Illuminate\Foundation\Http\FormRequest;

class ReportStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'session_id' => 'required|string|uuid',
            'report_type' => 'required|string|in:summary,detailed,heatmap',
            'include_heatmap' => 'nullable|boolean',
            'include_timeline' => 'nullable|boolean',
        ];
    }

    /**
     * @return ReportDTO
     */
    public function toDTO(): ReportDTO
    {
        return new ReportDTO(
            userId: $this->user()->id,
            sessionId: $this->input('session_id'),
            reportType: $this->input('report_type'),
            includeHeatmap: $this->input('include_heatmap', false),
            includeTimeline: $this->input('include_timeline', false),
        );
    }
}

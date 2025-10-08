<?php

namespace App\Http\Requests;

use App\Constants\CrawlerTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CrawlerResultReceiveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //Authorize with VerifyCrawlerToken middleware
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
            'type' => ['required', Rule::in(CrawlerTypes::all())],
            'original_url' => 'required|string',
            'final_url' => 'nullable|string',
            'content' => 'nullable|array',
            'error' => 'nullable|string',
            'is_last' => 'required|boolean',
            'status_code' => 'required|integer',
            'meta' => 'required|array',
            'first_step' => 'nullable|boolean'
        ];
    }
}

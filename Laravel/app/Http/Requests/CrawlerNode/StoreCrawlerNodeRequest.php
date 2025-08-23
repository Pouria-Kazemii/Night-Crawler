<?php

namespace App\Http\Requests\CrawlerNode;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCrawlerNodeRequest extends FormRequest
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
            'name'        => ['required', 'string', 'min:3', 'max:50'],
            'ip_address'  => ['required', 'ip', 'unique:crawler_nodes,ip_address'],
            'port'        => ['required', 'integer'],
            'protocol'    => ['required', Rule::in(['HTTP', 'HTTPS', 'SOCKS5'])],
            'status'      => ['nullable', Rule::in(['active', 'inactive', 'banned', 'down'])],
            'is_verified' => ['required', 'boolean'], 
            'location'    => ['required', 'string'],
        ];
    }

        public function messages(): array
    {
        return [
            'ip_address.unique' => 'این IP قبلاً ثبت شده است.',
        ];
    }
}

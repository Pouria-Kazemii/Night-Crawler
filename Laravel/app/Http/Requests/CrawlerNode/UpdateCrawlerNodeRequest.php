<?php

namespace App\Http\Requests\CrawlerNode;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCrawlerNodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'min:3', 'max:50'],
            'ip_address'  => [
                'required',
                'ip',
                Rule::unique('crawler_nodes', 'ip_address')->ignore($this->route('crawlerNode')->_id, '_id'),
            ],
            'port'        => ['required', 'integer'],
            'protocol'    => ['required', Rule::in(['HTTP', 'HTTPS', 'SOCKS5'])],
            'status'      => ['nullable', Rule::in(['active', 'inactive', 'banned', 'down'])],
            'is_verified' => ['required', 'boolean'],
            'location'    => ['required', 'string'],
        ];
    }
}


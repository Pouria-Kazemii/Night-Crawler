<?php

namespace App\Http\Requests\Crawler;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCrawlerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare JSON string fields (like selectors, auth, api_config) for validation as arrays.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'selectors' => $this->decodeJsonField('selectors'),
            'auth' => $this->decodeJsonField('auth'),
            'api_config' => $this->decodeJsonField('api_config'),
            'pagination_rule' => $this->decodeJsonField('pagination_rule'),
        ]);
    }

    /**
     * Decode a JSON field if it's a string.
     */
    protected function decodeJsonField(string $field): mixed
    {
        $value = $this->input($field);

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        return $value;
    }

    public function rules(): array
    {
        $type = $this->input('crawler_type');

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'crawler_status' => ['required', Rule::in(['active', 'paused', 'completed', 'error'])],
            'crawler_type' => ['required', Rule::in(['static', 'dynamic', 'paginated', 'authenticated', 'api', 'seed'])],
            'base_url' => ['required', 'url'],
            'start_urls' => ['required', 'array'],
            'start_urls.*' => ['url'],

            // Conditional JSON fields (decoded to arrays)
            'selectors' => $this->needsSelectors($type) ? ['required', 'array'] : ['nullable', 'array'],
            'pagination_rule' => $this->needsPagination($type) ? ['required', 'array'] : ['nullable', 'array'],
            'auth' => $type === 'authenticated' ? ['required', 'array'] : ['nullable', 'array'],
            'api_config' => $type === 'api' ? ['required', 'array'] : ['nullable', 'array'],

            'schedule' => ['required', 'array'],
            'schedule.frequency' => ['required', 'string'],
            'schedule.time' => ['required', 'string'],

            'max_depth' => ['nullable', 'integer', 'min:0'],
            'link_filter_rules' => ['nullable', 'array'],
            'link_filter_rules.*' => ['string'],
            'crawl_delay' => ['nullable', 'integer', 'min:0']
        ];
    }

    protected function needsSelectors(string $type): bool
    {
        return in_array($type, ['static', 'dynamic', 'paginated', 'authenticated']);
    }

    protected function needsPagination(string $type): bool
    {
        return $type === 'paginated';
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الزامی است.',
            'crawler_status.required' => 'وضعیت خزنده الزامی است.',
            'crawler_type.required' => 'نوع خزنده را وارد کنید.',
            'base_url.required' => 'آدرس پایه الزامی است.',
            'start_urls.required' => 'آدرس‌های شروع الزامی هستند.',
            'selectors.required' => 'فیلد انتخاب‌گرها برای این نوع خزنده الزامی است.',
            'auth.required' => 'اطلاعات احراز هویت برای این نوع خزنده الزامی است.',
            'api_config.required' => 'پیکربندی API برای این نوع خزنده الزامی است.',
            'pagination_rule.required' => 'قانون صفحه‌بندی برای این نوع خزنده الزامی است.',
        ];
    }
}

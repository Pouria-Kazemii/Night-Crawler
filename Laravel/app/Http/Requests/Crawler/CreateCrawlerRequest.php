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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'auth' => $this->decodeJsonField('auth'),
            'api_config' => $this->decodeJsonField('api_config'),
            'pagination_rule' => $this->decodeJsonField('pagination_rule'),
        ]);

        $type = $this->input('crawler_type');

        if ($type !== 'seed') {
            $this->request->remove('max_depth');
            $this->request->remove('link_filter_rules');
        }
    }

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

            'start_urls' => ['nullable', 'array'],
            'start_urls.*' => ['nullable','string'],

            'url_pattern' => ['nullable', 'string'],
            'range.start' => ['nullable', 'integer', 'min:1'],
            'range.end' => ['nullable', 'integer', 'min:1'],

            'pagination_rule' => $type === 'paginated' ? ['required', 'array'] : ['nullable', 'array'],
            'pagination_rule.next_page_selector'=> $type === 'paginated' ? ['required', 'string'] : ['nullable', 'string'],
            'pagination_rule.limit' =>   ['nullable', 'integer'],

            'auth' => $type === 'authenticated' ? ['required', 'array'] : ['nullable', 'array'],
            'auth.login_url' => $type === 'authenticated' ? ['required', 'url'] : ['nullable', 'url'],
            'auth.username' => $type === 'authenticated' ? ['required'] : ['nullable'],
            'auth.password' => $type === 'authenticated' ? ['required'] : ['nullable'],
            
            'api_config' => $type === 'api' ? ['required', 'array'] : ['nullable', 'array'],//TODO 

            'selectors' => ($type === 'dynamic' || $type === 'static' || $type === 'paginated' || $type === 'authenticated') 
            ? ['required', 'array'] 
            : ['nullable', 'array'],


            'schedule' => ['nullable', 'array'],
            'schedule.frequency' => ['nullable', 'string'],
            'schedule.time' => ['nullable', 'string'],

            'crawl_delay' => ['nullable', 'integer', 'min:0'],

            'max_depth' => $type === 'seed' ? ['nullable', 'integer', 'min:0'] : ['prohibited'],
            'link_filter_rules' => $type === 'seed' ? ['nullable', 'array'] : ['prohibited'],
            'link_filter_rules.*' => ['string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasUrlPattern = filled($this->input('url_pattern')) && filled($this->input('range.start')) && filled($this->input('range.end'));

            if ($this->input('start_urls')[0]!=null && $this->input('url_pattern')!=null) {
                $validator->errors()->add('start_urls', 'نمی‌توانید هم آدرس‌های شروع و هم الگوی URL را وارد کنید.');
                $validator->errors()->add('url_pattern', 'نمی‌توانید هم الگوی URL و هم آدرس‌های شروع را وارد کنید.');
            }

            if ($hasUrlPattern && !str_contains($this->input('url_pattern'), '{id}')) {
                $validator->errors()->add('url_pattern', 'الگوی URL باید شامل {id} باشد.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان الزامی است.',
            'crawler_status.required' => 'وضعیت خزنده الزامی است.',
            'crawler_type.required' => 'نوع خزنده را وارد کنید.',
            'base_url.required' => 'آدرس پایه الزامی است.',

            'pagination_rule.required' => 'قانون صفحه‌بندی برای این نوع خزنده الزامی است.',
            'pagination_rule.next_page_selector.required' => 'کد css دکمه ی بعدی برای این نوع خزنده الزامی است',
            'pagination_rule.limit' => 'حداکثر صفحات باید عدد باشد',

            'auth.required' => 'اطلاعات احراز هویت برای این نوع خزنده الزامی است.',
            'auth.login_url.required' => 'آدرس صفحه ی ورود باری این نوع خزنده الزامی است',
            'auth.login_url.url' => 'آدرس صفحه ی ورود فرمت درستی ندارد',
            'auth.username.required' => 'وارد کردن نام کاربری برای این نوع خزشگر الزامی میباشد',
            'auth.password.required' => 'وارد کردن رمز عبور برای این نوع خرشگر الزامی میباشد',

            'api_config.required' => 'پیکربندی API برای این نوع خزنده الزامی است.',

            'selectors.required' => 'انتخاب کنندها برای این نوع از خزشگر الزامی میباشد',
            'selectors.array' => 'فرمت انتخاب کننده‌ها صحیح نیست',
            'selectors.*.key.required' => 'کلید انتخاب کننده الزامی است',
            'selectors.*.selector.required' => 'مقدار انتخاب کننده الزامی است',


            'max_depth.prohibited' => 'عمق خزش فقط برای خزنده نوع seed مجاز است.',
            'link_filter_rules.prohibited' => 'قوانین فیلتر لینک فقط برای خزنده نوع seed مجاز است.',
        ];
    }
}

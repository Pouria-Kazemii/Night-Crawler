<?php

namespace App\Http\Requests\Crawler;

use App\Constants\CrawlerTypes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCrawlerRequest extends FormRequest
{
    private string $type;
    private string $two_step_first;
    private string $two_step_second;

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
            'two_step' => $this->decodeJsonField('two_step')
        ]);

        $this->type = $this->input('crawler_type');

        $this->two_step_first  = $this->input('two_step')['first'] ?? 'null';

        $this->two_step_second = $this->input('two_step')['second'] ?? 'null';

        if (!$this->check(['seed'])) {
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
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'crawler_status' => ['required', Rule::in(['active', 'paused', 'completed', 'error', 'running', 'first_step_done'])],
            'crawler_type' => ['required', Rule::in(CrawlerTypes::ALL_STEP)],
            'base_url' => ['required', 'url'],

            'start_urls' => ['nullable', 'array'],
            'start_urls.*' => ['nullable', 'string'],

            'url_pattern' => ['nullable', 'string'],
            'range.start' => ['nullable', 'integer', 'min:1'],
            'range.end' => ['nullable', 'integer', 'min:1'],

            'pagination_rule' => $this->check(['paginated']) ? ['required', 'array'] : ['nullable', 'array'],
            'pagination_rule.next_page_selector' => $this->check(['paginated']) ? ['required', 'string'] : ['nullable', 'string'],
            'pagination_rule.limit' =>   ['nullable', 'integer'],

            'auth' => $this->check(['authenticated']) ? ['required', 'array'] : ['nullable', 'array'],
            'auth.login_url' => $this->check(['authenticated']) ? ['required', 'url'] : ['nullable', 'url'],
            'auth.username' => $this->check(['authenticated']) ? ['required'] : ['nullable'],
            'auth.password' => $this->check(['authenticated']) ? ['required'] : ['nullable'],
            'auth.username_selector' => $this->check(['authenticated']) ? ['required'] : ['nullable'],
            'auth.password_selector' => $this->check(['authenticated']) ? ['required'] : ['nullable'],


            'api_config' => $this->check(['api']) ? ['required', 'array'] : ['nullable', 'array'], //TODO

            'dynamic_limit' => $this->check(['dynamic']) ? ['required', 'integer'] : ['nullable', 'integer'],

            'selectors' => $this->check(CrawlerTypes::SELECTOR)
                ? ['required', 'array']
                : ['nullable', 'array'],


            'array_selector' => $this->check(CrawlerTypes::SELECTOR)
                ? ['required', Rule::in(['true', 'false'])]
                : ['nullable', Rule::in(['true', 'false'])],

            'link_selector' => ['nullable', 'string'],

            'two_step' => $this->check(['two_step']) ? ['required', 'array'] : ['nullable', 'array'],
            'two_step.first' => $this->check(['two_step']) ? ['required', Rule::in(CrawlerTypes::FIRST_STEP)] : ['nullable', Rule::in(CrawlerTypes::FIRST_STEP)],
            'two_step.second' => $this->check(['two_step']) ? ['required', Rule::in(CrawlerTypes::SECOND_STEP)] : ['nullable', Rule::in(CrawlerTypes::SECOND_STEP)],
            'just_new_data' => $this->check(['two_step'])  ? ['required', Rule::in(['true', 'false'])] : ['nullable', Rule::in(['true', 'false'])],

            'schedule' => ['nullable', 'integer'],

            'crawl_delay' => ['nullable', 'integer', 'min:0'],

            'link_filter_rules' => $this->check(['seed']) ? ['nullable', 'array'] : ['prohibited'],
            'link_filter_rules.*' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $hasUrlPattern = filled($this->input('url_pattern')) && filled($this->input('range.start')) && filled($this->input('range.end'));

            if ($this->input('start_urls')[0] != null && $this->input('url_pattern') != null) {
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
            'auth.login_url.required' => 'آدرس صفحه ی ورود برای این نوع خزنده الزامی است',
            'auth.login_url.url' => 'آدرس صفحه ی ورود فرمت درستی ندارد',
            'auth.username.required' => 'وارد کردن نام کاربری برای این نوع خزشگر الزامی میباشد',
            'auth.password.required' => 'وارد کردن رمز عبور برای این نوع خرشگر الزامی میباشد',
            'auth.username_selector.required' => 'وارد کردن اطلاعات انتخاب کننده نام کاربری برای این نوع خزشگر الزامی میباشد',
            'auth.password_selector.required' => 'وارد کردن اطلاعات انتخاب کننده رمزعبور برای این نوع خزشگر الزامی میباشد',

            'api_config.required' => 'پیکربندی API برای این نوع خزنده الزامی است.',

            'selectors.required' => 'انتخاب کنندها برای این نوع از خزشگر الزامی میباشد',
            'selectors.array' => 'فرمت انتخاب کننده‌ها صحیح نیست',
            'selectors.*.key.required' => 'کلید انتخاب کننده الزامی است',
            'selectors.*.selector.required' => 'مقدار انتخاب کننده الزامی است',


            'link_filter_rules.prohibited' => 'قوانین فیلتر لینک فقط برای خزنده نوع seed مجاز است.',

            'dynamic_limit.required' => 'انتخاب تعداد بارگیری های مجدد برای این نوع خزشگر الزامی میباشد',

            'two_step.first.required' => 'انتخاب مرحله اول برای این نوع از خزشگر الزامی است',
            'two_step.second.required' => 'انتخاب مرحله دوم برای این نوع از خزشگر الزامی است',
            'just_new_data.required' => 'لطفا مقدار را انتخاب کنید',
            'array_selector.required' => 'لطفا مقدار را انتخاب کنید'

        ];
    }

    private function check(array $types): bool
    {
        return !empty(array_intersect([$this->type, $this->two_step_first, $this->two_step_second], $types));
    }
}

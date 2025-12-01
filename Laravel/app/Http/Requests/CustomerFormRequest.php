<?php

namespace App\Http\Requests;

use App\Models\Subscribe;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'available_crawler_ids' => $this->input('available_crawler_ids', []),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $customerId = $this->route('customer')?->id;

        return [
            'name' => ['required', 'string', 'min:3', 'max:110'],
            'url' =>  [
                'required',
                'url',
                Rule::unique('customers', 'url')->ignore($customerId)
            ],
            'ip_address' => [
                'required',
                'ip',
                Rule::unique('customers', 'ip_address')->ignore($customerId)
            ],
            'available_crawler_ids' => ['nullable', 'array'],
            'available_crawler_ids.*' => [Rule::exists('crawlers', 'id')],
            'subscribe_uct' => [
                'required',
                'string',
                Rule::exists('subscribes', 'uct'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'عنوان الزامی است',
            'name.min' => 'عنوان باید حداقل 3 کاراکتر باشد',
            'url.url' => 'لینک معتبر نمیباشد',
            'url.unique' => 'لینک تکراری است',
            'ip_address.ip' => 'مقدار آیپی معتبر نمیباشد',
            'ip_address.unique' => 'آیپی تکراری است',
            'subscribe_uct.required' => 'نوع اشتراک الزامی است',
            'subscribe_uct.exists' => 'این نوع اشتراک وجود ندارد',
            'available_crawler_ids.*.exists' => 'مقدار خزشگر وارد شده موجود نمیباشد'
        ];
    }
}

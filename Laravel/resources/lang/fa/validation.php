<?php

return [
    'required' => ':attribute الزامی است.',
    'email'    => ':attribute باید یک ایمیل معتبر باشد.',
    'min'      =>  ':attribute باید حداقل :min کاراکتر باشد.',
    'max'      => ':attribute نباید بیشتر از :max کاراکتر باشد.',
    'confirmed' => ':attribute مطابقت ندارد.',
    'current_password' => 'رمز عبور فعلی مطابفت ندارد',
    'unique' => ':attribute قبلاً استفاده شده است.',
    'ip'     => ':attribute باید یک IP معتبر باشد.',
    'in'     => ':attribute نامعتبر است.',
    'boolean' => ':attribute باید صحیح یا غلط باشد.',

    'password' => [
        'min' => ':attribute باید حداقل :min کاراکتر باشد.',
        'letters' => ':attribute باید حداقل یک حرف داشته باشد.',
        'mixed' => ':attribute باید شامل حروف بزرگ و کوچک باشد.',
        'numbers' => ':attribute باید حداقل یک عدد داشته باشد.',
        'symbols' => ':attribute باید حداقل یک نماد داشته باشد.',
        'uncompromised' => ':attribute در یک نشت داده ظاهر شده است. لطفاً رمز عبور متفاوتی انتخاب کنید.',
    ],

    'attributes' => [
        'email' => 'ایمیل',
        'password' => 'رمز عبور',
        'name' => 'نام',
        'current_password' => 'رمز عبور فعلی',
        'ip_address'  => 'آدرس IP',
        'port'        => 'پورت',
        'protocol'    => 'پروتکل',
        'status'      => 'وضعیت',
        'is_verified' => 'تأیید شده',
        'location'    => 'موقعیت',
    ],
];

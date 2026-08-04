<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'string' => 'يجب أن يكون حقل :attribute نصاً.',
    'integer' => 'يجب أن يكون حقل :attribute رقماً صحيحاً.',
    'numeric' => 'يجب أن يكون حقل :attribute رقماً.',
    'date' => 'يجب أن يكون حقل :attribute تاريخاً صحيحاً.',
    'email' => 'يجب أن يكون حقل :attribute بريداً إلكترونياً صحيحاً.',
    'exists' => 'القيمة المحددة في :attribute غير صحيحة.',
    'unique' => 'قيمة :attribute مستخدمة مسبقاً.',
    'after_or_equal' => 'يجب أن يكون :attribute بعد أو مساوياً لـ :date.',
    'between' => ['numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.'],
    'min' => ['numeric' => 'يجب ألا تقل قيمة :attribute عن :min.', 'string' => 'يجب ألا يقل :attribute عن :min أحرف.'],
    'max' => ['numeric' => 'يجب ألا تزيد قيمة :attribute عن :max.', 'string' => 'يجب ألا يزيد :attribute عن :max حرفاً.', 'file' => 'يجب ألا يزيد حجم :attribute عن :max كيلوبايت.'],
    'attributes' => [
        'company_id' => 'الشركة', 'administration_id' => 'الإدارة', 'invoice_number' => 'رقم الفاتورة',
        'invoice_date' => 'تاريخ الفاتورة', 'invoice_month' => 'الشهر', 'received_date' => 'تاريخ الاستلام',
        'financial_return_date' => 'تاريخ الإرجاع للمالية', 'amount' => 'المصروفات', 'exchange_rate' => 'سعر الصرف',
        'location' => 'الموقع', 'name' => 'الاسم', 'username' => 'اسم المستخدم', 'password' => 'كلمة المرور'
    ],
];

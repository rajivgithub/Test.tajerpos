<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'يجب قبول :attribute.',
    'accepted_if' => 'يجب قبول :attribute عندما يكون :other هو :value.',
    'active_url' => 'الـ :attribute ليس رابط صالح.',
    'after' => 'يجب أن يكون :attribute تاريخًا بعد :date.',
    'after_or_equal' => 'يجب أن يكون :attribute تاريخًا بعد أو يساوي :date.',
    'alpha' => 'يجب أن يحتوي :attribute فقط على حروف.',
    'alpha_dash' => 'يجب أن يحتوي :attribute فقط على الحروف والأرقام والشرطات والشرطات السفلية',
    'alpha_num' => 'يجب أن يحتوي :attribute فقط على الحروف والأرقام.',
    'alpha_special' => 'يجب أن يحتوي :attribute فقط على الحروف، الفاصلة، الشرطة (-)، علامة الوأد (&)، المسافة البيضاء والنقطة (.)',
    'alpha_space' => 'يجب أن يحتوي :attribute فقط على الحروف، المسافات البيضاء والنقطة (.)',
    'array' => 'يجب أن يكون :attribute مصفوفة.',
    'before' => 'يجب أن يكون :attribute تاريخًا قبل :date.',
    'before_or_equal' => 'يجب أن يكون :attribute تاريخًا قبل أو يساوي :date.',
    'between' => [
        'numeric' => 'يجب أن يكون :attribute بين :min و :max.',
        'file' => 'يجب أن يكون حجم :attribute بين :min و :max كيلوبايت.',
        'string' => 'يجب أن يكون :attribute بين :min و :max حرفًا.',
        'array' => 'يجب أن يحتوي :attribute على بين :min و :max عنصرًا.',
    ],
    'boolean' => 'يجب أن يكون حقل :attribute صحيح أو خاطئ.',
    'confirmed' => 'تأكيد :attribute غير متطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'التاريخ :attribute غير صالح.',
    'date_equals' => 'يجب أن يكون :attribute تاريخًا يساوي :date.',
    'date_format' => 'الـ :attribute لا يتطابق مع الشكل :format.',
    'declined' => 'يجب رفض :attribute.', 
    'declined_if' => 'يجب رفض :attribute عندما يكون :other هو :value.',
    'different' => 'يجب أن يكون :attribute و :other مختلفين.',
    'digits' => 'يجب أن يكون :attribute :digits أرقام.',
    'digits_between' => 'يجب أن يكون :attribute بين :min و :max أرقام.',
    'dimensions' => 'الأبعاد الصورية لـ :attribute غير صحيحة.',
    'distinct' => 'حقل :attribute يحتوي على قيمة مكررة.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح.',
    'ends_with' => 'يجب أن ينتهي :attribute بأحد القيم التالية: :values.',
    'enum' => 'الـ :attribute المحدد غير صالح.',
    'exists' => 'الـ :attribute المحدد غير صالح.',
    'file' => 'يجب أن يكون :attribute ملفًا.',
    'filled' => 'يجب أن يحتوي حقل :attribute على قيمة.',
    'gt' => [
        'numeric' => 'يجب أن يكون :attribute أكبر من :value.',
        'file' => 'يجب أن يكون :attribute أكبر من :value كيلوبايت.',
        'string' => 'يجب أن يكون :attribute أكبر من :value حرفًا.',
        'array' => 'يجب أن يحتوي :attribute على أكثر من :value عنصرًا.',
    ],
    'gte' => [
        'numeric' => 'يجب أن يكون :attribute أكبر من أو يساوي :value.',
        'file' => 'يجب أن يكون حجم :attribute أكبر من أو يساوي :value كيلوبايت.',
        'string' => 'يجب أن يكون :attribute أكبر من أو يساوي :value حرفًا.',
        'array' => 'يجب أن يحتوي :attribute على :value عنصر أو أكثر.',
    ],
    'image' => 'يجب أن يكون :attribute صورة.',
    'image_extension' => 'امتداد غير صالح. الامتدادات المدعومة هي: png، jpg، jpeg.',
    'in' => 'الـ :attribute المحدد غير صالح.',
    'in_array' => 'حقل :attribute لا يوجد في :other.',
    'integer' => 'يجب أن يكون :attribute عددًا صحيحًا.',
    'ip' => 'يجب أن يكون :attribute عنوان IP صحيحًا.',
    'ipv4' => 'يجب أن يكون :attribute عنوان IPv4 صحيحًا.',
    'ipv6' => 'يجب أن يكون :attribute عنوان IPv6 صحيحًا.',
    'json' => 'يجب أن يكون :attribute سلسلة JSON صحيحة.',
    'lt' => [
        'numeric' => 'يجب أن يكون :attribute أقل من :value.',
        'file' => 'يجب أن يكون حجم :attribute أقل من :value كيلوبايت.',
        'string' => 'يجب أن يكون :attribute أقل من :value حرفًا.',
        'array' => 'يجب أن يحتوي :attribute على أقل من :value عنصر.',
    ],
    'lte' => [
        'numeric' => 'يجب أن يكون :attribute أقل من أو يساوي :value.',
        'file' => 'يجب أن يكون حجم :attribute أقل من أو يساوي :value كيلوبايت.',
        'string' => 'يجب أن يكون :attribute أقل من أو يساوي :value حرفًا.',
        'array' => 'يجب أن لا يحتوي :attribute على أكثر من :value عنصر.',
    ],
    'mac_address' => 'يجب أن يكون :attribute عنوان MAC صحيحًا.',
    'max' => [
        'numeric' => 'يجب أن لا يكون :attribute أكبر من :max.',
        'file' => 'يجب أن لا يكون حجم :attribute أكبر من :max كيلوبايت.',
        'string' => 'يجب أن لا يكون :attribute أكبر من :max حرفًا.',
        'array' => 'يجب أن لا يحتوي :attribute على أكثر من :max عنصر.',
    ],
    'mimes' => 'يجب أن يكون :attribute ملفًا من النوع: :values.',
    'mimetypes' => 'يجب أن يكون :attribute ملفًا من النوع: :values.',
    'min' => [
        'numeric' => 'يجب أن يكون :attribute على الأقل :min.',
        'file' => 'يجب أن يكون حجم :attribute على الأقل :min كيلوبايت.',
        'string' => 'يجب أن يكون :attribute على الأقل :min حرفًا.',
        'array' => 'يجب أن يحتوي :attribute على الأقل :min عنصر.',
    ],
    'multiple_of' => 'يجب أن يكون :attribute مضاعفًا لـ :value.',
    'not_in' => 'الـ :attribute المحدد غير صالح.',
    'not_regex' => 'تنسيق :attribute غير صالح.',
    'numeric' => 'يجب أن يكون :attribute رقمًا.',
    'password' => 'كلمة المرور غير صحيحة.',
    'present' => 'يجب أن يكون حقل :attribute حاضرًا.',
    'prohibited' => 'حقل :attribute ممنوع.',
    'prohibited_if' => 'حقل :attribute ممنوع عندما يكون :other هو :value.',
    'prohibited_unless' => 'حقل :attribute ممنوع ما لم يكن :other في :values.',
    'prohibits' => 'حقل :attribute يمنع وجود :other.',
    'regex' => 'تنسيق :attribute غير صالح.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'يجب أن يحتوي حقل :attribute على إدخالات للمفاتيح: :values.',
    'required_if' => 'يجب أن يكون حقل :attribute مطلوبًا عندما يكون :other هو :value.',
    'required_unless' => 'يجب أن يكون حقل :attribute مطلوبًا ما لم يكن :other في :values.',
    'required_with' => 'يجب أن يكون حقل :attribute مطلوبًا عندما يكون :values حاضرًا.',
    'required_with_all' => 'يجب أن يكون حقل :attribute مطلوبًا عندما تكون :values حاضرةً.',
    'required_without' => 'يجب أن يكون حقل :attribute مطلوبًا عندما لا تكون :values حاضرةً.',
    'required_without_all' => 'يجب أن يكون حقل :attribute مطلوبًا عندما لا تكون أي من :values حاضرةً.',
    'same' => 'يجب أن يتطابق :attribute و :other.',
    'size' => [
        'numeric' => 'يجب أن يكون :attribute :size.',
        'file' => 'يجب أن يكون حجم :attribute :size كيلوبايت.',
        'string' => 'يجب أن يكون :attribute :size حرفًا.',
        'array' => 'يجب أن يحتوي :attribute على :size عنصر.',
    ],
    'starts_with' => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون :attribute نصًا.',
    'timezone' => 'يجب أن يكون :attribute منطقة زمنية صحيحة.',
    'unique' => 'الـ :attribute مستخدم بالفعل.',
    'uploaded' => 'فشل تحميل :attribute.',
    'url' => 'يجب أن يكون :attribute رابطًا صحيحًا.',
    'uuid' => 'يجب أن يكون :attribute UUID صحيحًا.',    
    'email_invalid_msg' => 'الرجاء استخدام الحروف فقط (A-Z، a-z)، الحروف العربية، الأرقام (0-9)، والرموز التالية: _ . % + - @',
    'pwd_invalid_msg' => 'الرجاء استخدام الحروف فقط (A-Z، a-z)، الحروف العربية، الأرقام (0-9)، والرموز التالية: ! @ # $ % ^ & * _ = . , ~ / < : ; ? + -',
    'invalid_name_err' => 'الرجاء استخدام الحروف فقط (A-Z، a-z)، الحروف العربية، المسافات والنقطة (.)',
    'invalid_company_name_err' => "الرجاء استخدام الحروف فقط (A-Z، a-z)، الحروف العربية، الأرقام (0-9)، المسافات والرموز التالية: ' - . & ( )",
    'invalid_address_err' => "الرجاء استخدام الحروف فقط (A-Z، a-z)، الحروف العربية، الأرقام (0-9)، المسافات، والرموز التالية: ' , . / & ( ) + - ",
    'invalid_numeric_err' => 'الرجاء استخدام الأرقام فقط (0-9)',
    'invalid_url_err' => "الرجاء استخدام الحروف فقط (A-Z، a-z)، الحروف العربية، الأرقام (0-9) والرموز التالية: . % / -",
    'invalid_category_err' => "الرجاء استخدام الحروف فقط (A-Z، a-z)، الحروف العربية، الأرقام (0-9)، المسافات والرموز التالية: _ , - & . / + ( ) |",
    'description_invalid_msg' => 'يرجى استخدام الحروف فقط (A-Z، a-z)، الحروف العربية، الأرقام (0-9)، المسافات، والرموز التالية: . , ! @ # % ^ & * ( ) _ + = - : ; " \' < > / [ ] ? | { }',
    'file_extensions_err' => 'الملف :file_name لا يتطابق مع امتدادات الملفات المقبولة: :extensions',
    'mime_types_err' => 'الملف :file_name لا يتطابق مع أنواع MIME المقبولة: :mimes',
    'max_file_size_err' => 'الملف :file_name تجاوز الحد الأقصى للحجم الذي يبلغ :max ميجابايت',
    'max_file_count_err' => 'تعذرت إضافة الملف :file_name لأن الحد الأقصى المسموح به هو :max ملفات',
    'invalid_discount_code' => 'الرجاء استخدام الأحرف فقط (A-Z, a-z)، الأحرف العربية، الأرقام (0-9)، شرطة سفلية (_)، وشرطة (-)',
    'invalid_amount_err' => 'الرجاء استخدام الأرقام فقط (0-9) والنقطة العشرية (.)',
    'invalid_status_err' => 'يرجى استخدام الحروف فقط (من A إلى Z، من a إلى z)، الحروف العربية، المسافة، والرموز التالية: - \'',
    'invalid_string_err' => 'الرجاء استخدام الأحرف فقط (A-Z، a-z) والأرقام (0-9)',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];

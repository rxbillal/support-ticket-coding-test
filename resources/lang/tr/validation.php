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

    'accepted'             => 'The :attribute kabul edilmelidir.',
    'active_url'           => 'The :attribute eçerli bir URL değil.',
    'after'                => 'The :attribute şu tarihten sonraki bir tarih olmalıdır :date.',
    'after_or_equal'       => 'The :attribute tarihinden sonra veya ona eşit olmalıdır. :date.',
    'alpha'                => 'The :attribute yalnızca harf içerebilir.',
    'alpha_dash'           => 'The :attribute yalnızca harf, rakam, kısa çizgi ve alt çizgi içerebilir.',
    'alpha_num'            => 'The :attribute yalnızca harf ve rakam içerebilir.',
    'array'                => 'The :attribute bir dizi olmalıdır.',
    'before'               => 'The :attribute tarih öncesi bir tarih olmalıdır. :date.',
    'before_or_equal'      => 'The :attribute tarih öncesinde veya bu tarihe eşit olmalıdır :date.',
    'between'              => [
        'numeric' => 'The :attribute arasında olmalı :min and :max.',
        'file'    => 'The :attribute arasında olmalı :min and :max kilobytes.',
        'string'  => 'The :attribute arasında olmalı :min and :max characters.',
        'array'   => 'The :attribute arasında olmalı :min and :max items.',
    ],
    'boolean'              => 'The :attribute alanı doğru veya yanlış olmalıdır',
    'confirmed'            => 'The :attribute öznitelik onayı eşleşmiyor',
    'date'                 => 'The :attribute geçerli bir tarih değil',
    'date_equals'          => 'The :attribute tarihe eşit bir tarih olmalıdır. :date.',
    'date_format'          => 'The :attribute formatla eşleşmiyor :format.',
    'different'            => 'The :attribute and :other farklı olmalı.',
    'digits'               => 'The :attribute olmalıdır :digits digits.',
    'digits_between'       => 'The :attribute arasında olmalı :min and :max digits.',
    'dimensions'           => 'The :attribute geçersiz resim boyutlarına sahip.',
    'distinct'             => 'The :attribute alanında yinelenen bir değer var.',
    'email'                => 'The :attribute Geçerli bir e-posta adresi olmalı.',
    'ends_with'            => 'The :attribute aşağıdakilerden biriyle bitmelidir: :values.',
    'exists'               => 'The seçildi :attribute geçersizdir.',
    'file'                 => 'The :attribute bir dosya olmalıdır.',
    'filled'               => 'The :attribute alan bir değere sahip olmalıdır.',
    'gt'                   => [
        'numeric' => 'The :attribute şundan büyük olmalı :value.',
        'file'    => 'The :attribute şundan büyük olmalı :value kilobytes.',
        'string'  => 'The :attribute şundan büyük olmalı :value characters.',
        'array'   => 'The :attribute daha fazlasına sahip olmalı :value items.',
    ],
    'gte'                  => [
        'numeric' => 'The :attribute büyük veya eşit olmalıdır :value.',
        'file'    => 'The :attribute büyük veya eşit olmalıdır :value kilobytes.',
        'string'  => 'The :attribute büyük veya eşit olmalıdır :value characters.',
        'array'   => 'The :attribute sahip olmalı :value items or more.',
    ],
    'image'                => 'The :attribute bir resim olmalı.',
    'in'                   => 'The seçildi :attribute geçersizdir.',
    'in_array'             => 'The :attribute alan mevcut değil :other.',
    'integer'              => 'The :attribute tam sayı olmak zorunda.',
    'ip'                   => 'The :attribute geçerli bir IP adresi olmalıdır.',
    'ipv4'                 => 'The :attribute geçerli bir IPv4 adresi olmalıdır.',
    'ipv6'                 => 'The :attribute geçerli bir IPv6 adresi olmalıdır.',
    'json'                 => 'The :attribute geçerli bir JSON dizesi olmalıdır.',
    'lt'                   => [
        'numeric' => 'The :attribute daha az olmalı :value.',
        'file'    => 'The :attribute daha az olmalı :value kilobytes.',
        'string'  => 'The :attribute daha az olmalı :value characters.',
        'array'   => 'The :attribute daha az olmalı :value items.',
    ],
    'lte'                  => [
        'numeric' => 'The :attribute küçük veya eşit olmalıdır :value.',
        'file'    => 'The :attribute küçük veya eşit olmalıdır :value kilobytes.',
        'string'  => 'The :attribute küçük veya eşit olmalıdır :value characters.',
        'array'   => 'The :attribute küçük veya eşit olmalıdır :value items.',
    ],
    'max'                  => [
        'numeric' => 'The :attribute daha büyük olamaz :max.',
        'file'    => 'The :attribute daha büyük olamaz :max kilobytes.',
        'string'  => 'The :attribute daha büyük olamaz :max characters.',
        'array'   => 'The :attribute daha büyük olamaz :max items.',
    ],
    'mimes'                => 'The :attribute dosya türünde olmalı: :values.',
    'mimetypes'            => 'The :attribute dosya türünde olmalı: :values.',
    'min'                  => [
        'numeric' => 'The :attribute en azından olmalı :min.',
        'file'    => 'The :attribute en azından olmalı :min kilobytes.',
        'string'  => 'The :attribute en azından olmalı :min characters.',
        'array'   => 'The :attribute en azından olmalı :min items.',
    ],
    'not_in'               => 'The selected :attribute geçersizdir.',
    'not_regex'            => 'The :attribute format geçersizdir..',
    'numeric'              => 'The :attribute bir sayı olmalıdır.',
    'password'             => 'Şifre yanlış.',
    'present'              => 'The :attribute alan mevcut olmalıdır.',
    'regex'                => 'The :attribute format geçersiz.',
    'required'             => 'The :attribute alan gereklidir.',
    'required_if'          => 'The :attribute alan ne zaman gereklidir :other is :value.',
    'required_unless'      => 'The :attribute alan olmadıkça gereklidir :other is in :values.',
    'required_with'        => 'The :attribute alan ne zaman gereklidir :values is present.',
    'required_with_all'    => 'The :attribute alan ne zaman gereklidir :values are present.',
    'required_without'     => 'The :attribute alan ne zaman gereklidirn :values is not present.',
    'required_without_all' => 'The :attribute alan ne zaman gereklidir hiçbiri :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute olmalıdır :size.',
        'file'    => 'The :attribute olmalıdır :size kilobytes.',
        'string'  => 'The :attribute olmalıdır :size characters.',
        'array'   => 'The :attribute içermek zorundadır :size items.',
    ],
    'starts_with'          => 'The :attribute aşağıdakilerden biriyle başlamalı: :values.',
    'string'               => 'The :attribute bir dizi olmalı.',
    'timezone'             => 'The :attribute geçerli bir bölge olmalıdır.',
    'unique'               => 'The :attribute zaten alındı.',
    'uploaded'             => 'The :attribute yüklenemedi.',
    'url'                  => 'The :attribute format geçersiz.',
    'uuid'                 => 'The :attribute geçerli bir UUID olmalıdır.',

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
            'rule-name' => 'özel mesaj',
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

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines - Spanish
    |--------------------------------------------------------------------------
    |
    | El following language lines contain El default error messages used by
    | El validator class. Some of Else rules have multiple versions such
    | as El size rules. Feel free to tweak each of Else messages here.
    |
    */

    'accepted' => 'El :attribute debe ser aceptado.',
    'active_url' => 'El :attribute no es una URL válida.',
    'after' => 'El :attribute debe ser una fecha posterior :date.',
    'after_or_equal' => 'El :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El :attribute solo puede contener letras.',
    'alpha_dash' => 'El :attribute solo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El :attribute solo puede contener letras y números.',
    'array' => 'El :attribute debe ser una matriz.',
    'before' => 'El :attribute debe ser una fecha antes :date.',
    'before_or_equal' => 'El :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'numeric' => 'El :attribute debe estar entre :min and :max.',
        'file' => 'El :attribute debe estar entre :min and :max kilobytes.',
        'string' => 'El :attribute debe estar entre :min and :max caracteres.',
        'array' => 'El :attribute debe tener entre :min and :max artículos.',
    ],
    'boolean' => 'El :attribute el campo debe ser verdadero o falso.',
    'confirmed' => 'El :attribute la confirmación no coincide.',
    'date' => 'El :attribute no es una fecha válida.',
    'date_equals' => 'El :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El :attribute no coincide con el formato :format.',
    'different' => 'El :attribute and :other debe ser different.',
    'digits' => 'El :attribute debe ser :digits digits.',
    'digits_between' => 'El :attribute debe ser between :min and :max digits.',
    'dimensions' => 'El :attribute has invalid image dimensions.',
    'distinct' => 'El :attribute field has a duplicate value.',
    'email' => 'El :attribute debe ser a valid email address.',
    'ends_with' => 'El :attribute must end with one of El following: :values.',
    'exists' => 'El selected :attribute is invalid.',
    'file' => 'El :attribute debe ser a file.',
    'filled' => 'El :attribute field must have a value.',
    'gt' => [
        'numeric' => 'El :attribute debe ser greater than :value.',
        'file' => 'El :attribute debe ser greater than :value kilobytes.',
        'string' => 'El :attribute debe ser greater than :value caracteres.',
        'array' => 'El :attribute must have more than :value artículos.',
    ],
    'gte' => [
        'numeric' => 'El :attribute debe ser greater than or equal :value.',
        'file' => 'El :attribute debe ser greater than or equal :value kilobytes.',
        'string' => 'El :attribute debe ser greater than or equal :value caracteres.',
        'array' => 'El :attribute must have :value artículos or more.',
    ],
    'image' => 'El :attribute debe ser an image.',
    'in' => 'El selected :attribute is invalid.',
    'in_array' => 'El :attribute el campo no existe en :other.',
    'integer' => 'El :attribute debe ser un entero.',
    'ip' => 'El :attribute debe ser una dirección IP válida.',
    'ipv4' => 'El :attribute debe ser una dirección IPv4 válida.',
    'ipv6' => 'El :attribute debe ser una dirección IPv6 válida.',
    'json' => 'El :attribute debe ser una cadena JSON válida.',
    'lt' => [
        'numeric' => 'El :attribute debe ser menor que :value.',
        'file' => 'El :attribute debe ser menor que :value kilobytes.',
        'string' => 'El :attribute debe ser menor que :value caracteres.',
        'array' => 'El :attribute debe tener menos de :value artículos.',
    ],
    'lte' => [
        'numeric' => 'El :attribute debe ser menor que or equal :value.',
        'file' => 'El :attribute debe ser menor que or equal :value kilobytes.',
        'string' => 'El :attribute debe ser menor que or equal :value caracteres.',
        'array' => 'El :attribute no debe tener más de :value artículos.',
    ],
    'max' => [
        'numeric' => 'El :attribute puede no ser mayor que :max.',
        'file' => 'El :attribute puede no ser mayor que :max kilobytes.',
        'string' => 'El :attribute puede no ser mayor que :max caracteres.',
        'array' => 'El :attribute may not have more than :max artículos.',
    ],
    'mimes' => 'El :attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => 'El :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'numeric' => 'El :attribute debe ser por lo menos :min.',
        'file' => 'El :attribute debe ser por lo menos :min kilobytes.',
        'string' => 'El :attribute debe ser por lo menos :min caracteres.',
        'array' => 'El :attribute debe tener al menos :min artículos.',
    ],
    'not_in' => 'El seleccionado :attribute Es invalido.',
    'not_regex' => 'El :attribute el formato no es válido.',
    'numeric' => 'El :attribute Tiene que ser un número.',
    'password' => 'El La contraseña es incorrecta.',
    'present' => 'El :attribute el campo debe estar presente.',
    'regex' => 'El :attribute el formato no es válido.',
    'required' => 'El :attribute Se requiere campo.',
    'required_if' => 'El :attribute el campo es obligatorio cuando :other es :value.',
    'required_unless' => 'El :attribute el campo es obligatorio a menos que :other es en :values.',
    'required_with' => 'El :attribute el campo es obligatorio cuando :values es presente.',
    'required_with_all' => 'El :attribute el campo es obligatorio cuando :values están presentes.',
    'required_without' => 'El :attribute el campo es obligatorio cuando :values es no presente.',
    'required_without_all' => 'El :attribute El campo es obligatorio cuando ninguno de :values están presentes.',
    'same' => 'El :attribute y :other debe coincidir con.',
    'size' => [
        'numeric' => 'El :attribute debe ser :size.',
        'file' => 'El :attribute debe ser :size kilobytes.',
        'string' => 'El :attribute debe ser :size caracteres.',
        'array' => 'El :attribute debe contener :size artículos.',
    ],
    'starts_with' => 'El :attribute debe comenzar con uno de los siguientes: :values.',
    'string' => 'El :attribute debe ser una cuerda.',
    'timezone' => 'El :attribute debe ser una zona válida.',
    'unique' => 'El :attribute ya se ha tomado.',
    'uploaded' => 'El :attribute no se pudo cargar.',
    'url' => 'El :attribute el formato no es válido.',
    'uuid' => 'El :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using El
    | convention "attribute.rule" to name El lines. This makes it quick to
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
    | El following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];

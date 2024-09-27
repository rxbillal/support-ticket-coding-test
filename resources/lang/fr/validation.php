<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines - French
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Le :attribute doit être accepté.',
    'active_url' => 'Le :attribute n\'est pas une URL valide.',
    'after' => 'Le :attribute doit être une date après :date.',
    'after_or_equal' => 'Le :attribute doit être une date après or égal à :date.',
    'alpha' => 'Le :attribute ne peut contenir que des lettres.',
    'alpha_dash' => 'Le :attribute ne peut contenir que des lettres, nombres, tirets et traits de soulignement.',
    'alpha_num' => 'Le :attribute ne peut contenir que des lettres et chiffres.',
    'array' => 'Le :attribute doit être un tableau.',
    'before' => 'Le :attribute doit être une date antérieure :date.',
    'before_or_equal' => 'Le :attribute doit être une date antérieure or égal à :date.',
    'between' => [
        'numeric' => 'Le :attribute Doit être entre :min et :max.',
        'file' => 'Le :attribute Doit être entre :min et :max kilo-octets.',
        'string' => 'Le :attribute Doit être entre :min et :max personnages.',
        'array' => 'Le :attribute doit avoir entre :min et :max articles.',
    ],
    'boolean' => 'Le :attribute le champ doit être vrai ou faux.',
    'confirmed' => 'Le :attribute la confirmation ne correspond pas.',
    'date' => 'Le :attribute Ce n\'est pas une date valide.',
    'date_equals' => 'Le :attribute doit être une date égal à :date.',
    'date_format' => 'Le :attribute ne correspond pas Le format :format.',
    'different' => 'Le :attribute et :other doit être différent.',
    'digits' => 'Le :attribute doit être :digits chiffres.',
    'digits_between' => 'Le :attribute Doit être entre :min et :max chiffres.',
    'dimensions' => 'Le :attribute a des dimensions d\'image non valides.',
    'distinct' => 'Le :attribute le champ a une valeur en double.',
    'email' => 'Le :attribute Doit être une adresse e-mail valide.',
    'ends_with' => 'Le :attribute doit se terminer par l\'un des Le suivant: :values.',
    'exists' => 'Le choisi :attribute est invalide.',
    'file' => 'Le :attribute doit être un fichier.',
    'filled' => 'Le :attribute le champ doit avoir une valeur.',
    'gt' => [
        'numeric' => 'Le :attribute doit être supérieur à :value.',
        'file' => 'Le :attribute doit être supérieur à :value kilo-octets.',
        'string' => 'Le :attribute doit être supérieur à :value personnages.',
        'array' => 'Le :attribute doit avoir plus de :value articles.',
    ],
    'gte' => [
        'numeric' => 'Le :attribute doit être supérieur à or égal :value.',
        'file' => 'Le :attribute doit être supérieur à or égal :value kilo-octets.',
        'string' => 'Le :attribute doit être supérieur à or égal :value personnages.',
        'array' => 'Le :attribute doit avoir :value articles ou plus.',
    ],
    'image' => 'Le :attribute doit être une image.',
    'in' => 'Le choisi :attribute est invalide.',
    'in_array' => 'Le :attribute le champ n\'existe pas dans :other.',
    'integer' => 'Le :attribute Doit être un entier.',
    'ip' => 'Le :attribute doit être une adresse IP valide.',
    'ipv4' => 'Le :attribute doit être une adresse IPv4 valide.',
    'ipv6' => 'Le :attribute doit être une adresse IPv6 valide.',
    'json' => 'Le :attribute doit être une chaîne JSON valide.',
    'lt' => [
        'numeric' => 'Le :attribute doit être inférieur à :value.',
        'file' => 'Le :attribute doit être inférieur à :value kilo-octets.',
        'string' => 'Le :attribute doit être inférieur à :value personnages.',
        'array' => 'Le :attribute doit avoir moins de :value articles.',
    ],
    'lte' => [
        'numeric' => 'Le :attribute doit être inférieur à or égal :value.',
        'file' => 'Le :attribute doit être inférieur à or égal :value kilo-octets.',
        'string' => 'Le :attribute doit être inférieur à or égal :value personnages.',
        'array' => 'Le :attribute ne doit pas avoir plus de :value articles.',
    ],
    'max' => [
        'numeric' => 'Le :attribute ne peut pas être supérieur à :max.',
        'file' => 'Le :attribute ne peut pas être supérieur à :max kilo-octets.',
        'string' => 'Le :attribute ne peut pas être supérieur à :max personnages.',
        'array' => 'Le :attribute ne peut pas avoir plus de :max articles.',
    ],
    'mimes' => 'Le :attribute doit être un fichier de type: :values.',
    'mimetypes' => 'Le :attribute doit être un fichier de type: :values.',
    'min' => [
        'numeric' => 'Le :attribute doit être au moins :min.',
        'file' => 'Le :attribute doit être au moins :min kilo-octets.',
        'string' => 'Le :attribute doit être au moins :min personnages.',
        'array' => 'Le :attribute doit avoir au moins :min articles.',
    ],
    'not_in' => 'Le choisi :attribute est invalide.',
    'not_regex' => 'Le :attribute format est invalide.',
    'numeric' => 'Le :attribute doit être un nombre.',
    'password' => 'Le Le mot de passe est incorrect.',
    'present' => 'Le :attribute le champ doit être présent.',
    'regex' => 'Le :attribute format est invalide.',
    'required' => 'Le :attribute Champ requis.',
    'required_if' => 'Le :attribute Champ requis quand :other est :value.',
    'required_unless' => 'Le :attribute Champ requis unless :other est dans :values.',
    'required_with' => 'Le :attribute Champ requis quand :values est présent.',
    'required_with_all' => 'Le :attribute Champ requis quand :values sont présents.',
    'required_without' => 'Le :attribute Champ requis quand :values est pas présent.',
    'required_without_all' => 'Le :attribute Champ requis quand aucun de :values sont présents.',
    'same' => 'Le :attribute et :other doit correspondre.',
    'size' => [
        'numeric' => 'Le :attribute doit être :size.',
        'file' => 'Le :attribute doit être :size kilo-octets.',
        'string' => 'Le :attribute doit être :size personnages.',
        'array' => 'Le :attribute doit contenir :size articles.',
    ],
    'starts_with' => 'Le :attribute doit commencer par l\'un des Le suivant: :values.',
    'string' => 'Le :attribute doit être un string.',
    'timezone' => 'Le :attribute doit être une zone valide.',
    'unique' => 'Le :attribute a déjà été pris.',
    'uploaded' => 'Le :attribute échec du téléchargement.',
    'url' => 'Le :attribute format est invalide.',
    'uuid' => 'Le :attribute doit être a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using Le
    | convention "attribute.rule" to name Le lines. This makes it quick to
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

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

    'accepted'             => 'Das :attribute muss akzeptiert werden.',
    'active_url'           => 'Das :attribute ist keine gültige URL.',
    'after'                => 'Das :Attriattributebut muss ein Datum :date Datum sein.',
    'after_or_equal'       => 'Das :attribute muss ein Datum nach oder :date Datum sein.',
    'alpha'                => 'Das :attribute darf nur Buchstaben enthalten.',
    'alpha_dash'           => 'Das :attribute darf nur Buchstaben, Zahlen, Bindestriche und Unterstriche enthalten.',
    'alpha_num'            => 'Das :attribute darf nur Buchstaben und Zahlen enthalten.',
    'array'                => 'Das :attribute muss ein Array sein.',
    'before'               => 'Das :attribute muss ein Datum :date date sein.',
    'before_or_equal'      => 'Das :attribute muss ein Datum vor oder :date date sein.',
    'between'              => [
        'numeric' => 'Das :attribute muss zwischen sein :min und :max.',
        'file'    => 'Das :attribute muss zwischen sein :min und :max Kilobyte.',
        'string'  => 'Das :attribute muss zwischen sein :min und :max Zeichen.',
        'array'   => 'Das :attribute muss zwischen haben :min und :max Artikel.',
    ],
    'boolean'              => 'Das :attribute Feld muss wahr oder falsch sein.',
    'confirmed'            => 'Das :attribute Bestätigung stimmt nicht überein.',
    'date'                 => 'Das :attribute ist kein gültiges Datum.',
    'date_equals'          => 'Das :attribute muss ein Datum gleich sein :date.',
    'date_format'          => 'Das :attribute stimmt nicht mit dem Format überein :format.',
    'different'            => 'Das :attribute und :other muss anders sein',
    'digits'               => 'Das :attribute muss sein :digits Ziffern.',
    'digits_between'       => 'The :attribute muss zwischen sein :min und :max Ziffern.',
    'dimensions'           => 'Das :attribute hat ungültige Bildabmessungen.',
    'distinct'             => 'Das :attribute Feld hat einen doppelten Wert.',
    'email'                => 'Das :attribute muss eine gültige E-Mail-Adresse sein.',
    'ends_with'            => 'Das :attribute muss mit einer der folgenden enden: :values.',
    'exists'               => 'Das ausgewählt :attribute ist ungültig.',
    'file'                 => 'Das :attribute muss eine Datei sein.',
    'filled'               => 'Das :attribute Feld muss einen Wert haben.',
    'gt'                   => [
        'numeric' => 'Das :attribute muss größer sein als :value.',
        'file'    => 'Das :attribute muss größer sein als :value Kilobyte.',
        'string'  => 'Das :attribute muss größer sein als :value Zeichen.',
        'array'   => 'Das :attribute muss mehr haben als :value Artikel.',
    ],
    'gte'                  => [
        'numeric' => 'Das :attribute muss größer oder gleich sein :value.',
        'file'    => 'Das :attribute muss größer oder gleich sein :value Kilobyte.',
        'string'  => 'Das :attribute muss größer oder gleich sein :value Zeichen.',
        'array'   => 'Das :attribute haben müssen :value Gegenstände oder mehr.',
    ],
    'image'                => 'Das :attribute muss ein Bild sein.',
    'in'                   => 'Das ausgewählt :attribute ist ungültig.',
    'in_array'             => 'Das :attribute Feld existiert nicht in :other.',
    'integer'              => 'Das :attribute muss eine ganze Zahl sein.',
    'ip'                   => 'Das :attribute muss eine gültige IP-Adresse sein.',
    'ipv4'                 => 'Das :attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6'                 => 'Das :attribute muss eine gültige IPv6-Adresse sein.',
    'json'                 => 'Das :attribute muss eine gültige JSON-Zeichenfolge sein.',
    'lt'                   => [
        'numeric' => 'Das :attribute muss kleiner sein als :value.',
        'file'    => 'Das :attribute muss kleiner sein als :value Kilobyte.',
        'string'  => 'Das :attribute muss kleiner sein als :value Zeichen.',
        'array'   => 'Das :attribute muss weniger haben als :value Artikel.',
    ],
    'lte'                  => [
        'numeric' => 'Das :attribute muss kleiner oder gleich sein :value.',
        'file'    => 'Das :attribute muss kleiner oder gleich sein :value Kilobyte.',
        'string'  => 'Das :attribute muss kleiner oder gleich sein :value Zeichen.',
        'array'   => 'Das :attribute darf nicht mehr haben als :value Artikel.',
    ],
    'max'                  => [
        'numeric' => 'Das :attribute darf nicht größer sein als :max.',
        'file'    => 'Das :attribute darf nicht größer sein als :max Kilobyte.',
        'string'  => 'Das :attribute darf nicht größer sein als :max Zeichen.',
        'array'   => 'Das :attribute darf nicht mehr als haben :max Artikel.',
    ],
    'mimes'                => 'Das :attribute muss eine Datei vom Typ sein: :values.',
    'mimetypes'            => 'Das :attribute muss eine Datei vom Typ sein: :values.',
    'min'                  => [
        'numeric' => 'Das :attribute muss mindestens :min.',
        'file'    => 'Das :attribute muss mindestens :min Kilobyte.',
        'string'  => 'Das :attribute muss mindestens :min Zeichen.',
        'array'   => 'Das :attribute muss mindestens haben :min Artikel.',
    ],
    'not_in'               => 'Die gewählte :attribute ist ungültig.',
    'not_regex'            => 'Das :attribute Format ist ungültig.',
    'numeric'              => 'Das :attribute muss eine Nummer sein.',
    'password'             => 'Das Das Passwort ist inkorrekt.',
    'present'              => 'Das :attribute Feld muss vorhanden sein.',
    'regex'                => 'Das :attribute Format ist ungültig.',
    'required'             => 'Das :attribute Feld ist erforderlich.',
    'required_if'          => 'Das :attribute Feld ist erforderlich, wenn :other ist :value.',
    'required_unless'      => 'Das :attribute Feld ist erforderlich, es sei denn :other ist in :values.',
    'required_with'        => 'Das :attribute Feld ist erforderlich, wenn :values ist anwesend.',
    'required_with_all'    => 'Das :attribute fFeld ist erforderlich, wenn :values sind anwesend.',
    'required_without'     => 'Das :attribute Feld ist erforderlich, wenn :values ist nicht hier.',
    'required_without_all' => 'Das :attribute Feld ist erforderlich, wenn keines von :values sind anwesend.',
    'same'                 => 'Das :attribute und :other muss passen.',
    'size'                 => [
        'numeric' => 'Das :attribute muss sein :size.',
        'file'    => 'Das :attribute muss sein :size Kilobyte.',
        'string'  => 'Das :attribute muss sein :size Zeichen.',
        'array'   => 'Das :attribute muss sein :size Artikel.',
    ],
    'starts_with'          => 'Das :attribute muss mit einer der folgenden beginnen: :values.',
    'string'               => 'Das :attribute muss eine Zeichenfolge sein.',
    'timezone'             => 'Das :attribute muss eine gültige Zone sein.',
    'unique'               => 'Das :attribute wurde bereits genommen.',
    'uploaded'             => 'Das :attribute Upload fehlgeschlagen.',
    'url'                  => 'Das :attribute Format ist ungültig.',
    'uuid'                 => 'Das :attribute muss eine gültige UUID sein.',

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

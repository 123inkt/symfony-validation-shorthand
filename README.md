# Symfony Validation Shorthand
A validation shorthand component for Symfony, similar to the syntax in the "illuminate/validator" package for Laravel.

[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.1-8892BF)](https://php.net/)
[![Minimum Symfony Version](https://img.shields.io/badge/symfony-%3E%3D%204.4-brightgreen)](https://symfony.com/doc/current/validation.html)
![Run tests](https://github.com/123inkt/symfony-request-validation/workflows/Run%20tests/badge.svg)

## Installation
Include the library as dependency in your own project via: 
```
composer require "DigitalRevolution/SymfonyValidationShorthand"
```

## Usage

**Example**
```php
public function validate(array $data, ValidatorInterface $validator) {
    $rules = [
        'name.first_name' => 'required|string|min:5',
        'name.last_name'  => 'string|min:6',                                // last name is optional
        'email'           => 'required|email',
        'password'        => 'required|string|between:7,40',
        'phone_number'    => 'required|regex:/^020\d+$/',
        'news_letter'     => 'required|bool',
        'tags?.*'         => 'string'                                       // if tags is set, must be array of strings 
    ];        

    // construct a symfony constraint
    $constraint = (new ConstraintFactory)->fromRuleDefinitions($rules);
    $violations = $validator->validate($data, $constraint);
}    
```

Validates:
```
[
    'name'         => [
        'first_name' => 'Peter',
        'last_name'  => 'Parker'
    ],
    'email'        => 'example@example.com',
    'password'     => 'hunter8',
    'phone_number' => '0201234678',
    'news_letter'  => 'on',
    'tags'         => ['sports', 'movies', 'music']           
]
```

All available shorthands are [documented here](docs/available-shorthands.md)

## About us
At 123inkt (Part of Digital Revolution B.V.), every day more than 30 developers are working on improving our internal ERP and our several shops. Do you want to join us? [We are looking for developers](https://www.123inkt.nl/page/werken_ict.html).

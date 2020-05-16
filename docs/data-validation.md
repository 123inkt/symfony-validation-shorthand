| Index |
|:----------------- |
| [Shorthands](available-shorthands.md) |
| Data Validation |  

# Data Validation

## Basic example

Validating a key/value-pair data array.

Rules:
```php
[
    'productId'    =>  'required|int|min:1',                          
    'name'         =>  'required|string|filled|max:255',             
    'description'  =>  'string|filled|nullable|max:10000',           
    'active'       =>  'required|bool',
    'price'        =>  'required|float|min:0',
    'discount'     =>  'float|nullable'     
]
```

**Explanation:**

| Column       | Description                                                               |
|:-------------|:------------------------------------------------------------------------- |
| productId    | required and must be integer of 1 or higher                               |
| name         | required and must be non empty string less or equal than 255 characters   |
| description  | optional nullable or non empty string less or equal than 10000 characters |
| active       | required true/false                                                       |
| price        | required non nullable float greater or equal than zero                    |
| discount     | optional nullable float                                                   |

**Successfully validates:**
```php
[
    'productId'   => '12345',
    'name'        => 'Laser printer',
    'description' => null,
    'active'      => '1',
    'price'       => '4.99',
    'discount'    => '-1.00'
]
```

## Advanced example
```php
[
    'contact.first_name'      => 'required|string|filled',
    'contact.last_name'       => 'required|string|between:10,100',
    'contact.phone_number'    => 'required|regex:/^\d{10}$/',
    'contact.email'           => 'required|string|email',     
    'address.street_name'     => 'required|string',
    'address.house_number'    => 'required|int|between:0,999',
    'address.addition'        => 'string|nullable|max:3',
    'address.city'            => 'required|string|filled',
    'address.country_code'    => 'required|string|between:2,2',    
    'interests?.*.interestId' => 'required:int:min:1',
    'interests?.*.label'      => 'required|string|filled',                    
    'tags?.*'                 => 'required|string|filled'                   
]
```

**Explanation:**

The keys `contact` and `address` are mandatory, while `interests` and `tags` are optional.  
If `interested` is given, it must be an array of `[interestId, label]` elements  
If `tags` is given, it must be a non-empty array (required) of strings with minimum length of 1 (filled)

**Successfully validates**
```php
[
    'contact' => [
        'first_name'   => 'Peter',
        'last_name'    => 'Parker',  
        'phone_number' => '0123456789',
        'email'        => 'example@example.com',
    ],
    'address' => [
        'street_name'  => '15th Street',
        'house_number' => '24',
        'city'         => 'New York',
        'country_code' => 'US',
    ],
    'interests' => [
        ['interestId' => 5, 'label' => 'Movies'],
        ['interestId' => 7, 'label' => 'Photography'],        
    ],
    'tags' => ['customer', 'student', 'new']       
]
```  

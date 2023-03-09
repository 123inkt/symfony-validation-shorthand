| Index                                              |
|:-------------------------------------------------- |
|  Shorthands                                        |
| [Array data validation](data-validation.md)        |
| [Traversable data validation](traversable-data.md) |
| [Examples](examples.md)                            |

# Available shorthands
|General              |Type               |Range              |Pattern                  |Date                       |
|:--------------------|:------------------|:------------------|:------------------------|:--------------------------|
|[filled](#filled)    |[array](#array)    |[between](#between)|[alpha](#alpha)          |[date](#date)              |
|[nullable](#nullable)|[boolean](#boolean)|[max](#max)        |[alpha_dash](#alpha_dash)|[datetime](#datetime)      |
|[required](#required)|[float](#float)    |[min](#min)        |[alpha_num](#alpha_num)  |[date_format](#date_format)|
|                     |[integer](#integer)|                   |[email](#email)          |                           |
|                     |[string](#string)  |                   |[in](#in)                |                           |
|                     |                   |                   |[regex](#regex)          |                           |
|                     |                   |                   |[url](#url)              |                           |

## alpha
The field under validation must be entirely alphabetic characters. Shorthand for pattern: `[a-zA-Z]`

## alpha_dash
The field under validation may have alpha-numeric characters, as well as dashes and underscores. Shorthand for pattern: `[a-zA-Z0-9_-]`

## alpha_num
The field under validation must be entirely alpha-numeric characters. Shorthand for pattern: `[a-zA-Z0-9]`

## array
The field under validation must be a PHP array.

## between:
Arguments: `<digit>,<digit>`
    
The constraint has different implementations based on the value type.
- If the value has a date constraint (date, datetime or datetime_format), the `<digit>` arguments accepts now `DateTime`
  allowed formats. The value must be between than the supplied `<digit>` arguments.
  More information in the [Symfony Validation documentation](https://symfony.com/doc/current/reference/constraints/Range.html#date-ranges).
- If the value has a numeric constraint (integer or float), it must lie between the two values.
- Otherwise, the length of the value must be between the supplied values.

Example:
- string must have minimum length of 2 and maximum length of 6: `between:2,6`
- integer must have a value between 2 and 6 or less: `integer|between:2,6`
- date must be between `2010-01-01` and `2011-01-01`: `date|between:2010-01-01,2011-01-01`

## boolean
The value must be bool or castable to bool.
~~- allowed `true` values: `1, '1', 'on', true`~~
- allowed `false` values: `0, '0', 'off', false`  

Note: can also be written as `bool`

## date
The value must be a valid date of format `Y-m-d`

## datetime
The value must be a valid date+time of format `Y-m-d H:i:s`

## date_format
Argument: `<pattern>`

The value must match the given date pattern. See [DateTime::createFromFormat()](https://www.php.net/manual/en/datetime.createfromformat.php) for formatting options. 

## email
The value must be a valid email.

## filled
The value must be filled and not be null (except if [nullable](#nullable) is also set). If the value is an empty string, this validation rule fails.

## float
The value must be a float or castable to float.
- example of allowed values: `-1, 1, -1.1, 1.1, '1.1', '-1.1', '.1', '1.', '1', '-1'` 

## in
Arguments: `string,string,...`

The field under validation must be included in the given list of values. 

**Example:**
```
required|in:foo,bar
```

## integer
The value must be an integer or castable to int.
- example of allowed values: `1, -1, '1', '-1'`

Note: can also be written as `int`

## max:
Argument: `<digit>`  
  
The constraint has different implementations based on the value type.
- If the value has a date constraint (date, datetime or datetime_format), the `<digit>` argument accepts now `DateTime` 
  allowed formats and the value must be less or equal than the supplied argument. 
  More information in the [Symfony Validation documentation](https://symfony.com/doc/current/reference/constraints/LessThanOrEqual.html#comparing-dates).  
- If the value has a numeric constraint (integer or float), it must be smaller than the supplied value.
- Otherwise, the length of the value must be smaller than the supplied value.


Example:
 - string with maximum length of 6: `max:6`
 - integer which has to be 6 or less: `integer|max:6`
 - limit the given date by: `date|max:+10 days`

## min
Argument: `<value>`  

The constraint has different implementations based on the value type.
- If the value has a date constraint (date, datetime or datetime_format), the `<digit>` argument accepts now `DateTime`
  allowed formats and the value must be greater or equal than the supplied `<digit>` argument.
  More information in the [Symfony Validation documentation](https://symfony.com/doc/current/reference/constraints/GreaterThan.html#comparing-dates).
- If the value has a numeric constraint (integer or float), it must be bigger than the supplied value.
- Otherwise, the length of the value must be bigger than the supplied value.

Example:
- string with minimum length of 6: `min:6`
- integer which has to be 6 or higher: `integer|min:6`
- limit the given date by: `date|min:now`

## nullable
The value can be `null`.

## regex
Argument: `<pattern>`  

The value must match the supplied regex. The full string will be passed to the preg_match function.

Example:
- match all strings starting with 'ab': `regex:/^ab.*$/`

## required
By default in a data set a key/value-pair can be left out. Add `required` to make the key/value-pair mandatory.

**Example:**
```
[
    'a' => 'required|string',
    'b' => 'integer'
]
```
Passes:  
```
['a' => 'a'];
```  
Fails:
```
['b' => '1'];
```  

## string
The value must be a string.

## url
The value must be a valid url.

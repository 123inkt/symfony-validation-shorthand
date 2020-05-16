| Index |
|:----------------- |
|  Shorthands |
| [Data validation](data-validation.md) |

# Available shorthands
|General              |Type               |Range              |Pattern        |
|:--------------------|:------------------|:------------------|:--------------|
|[filled](#filled)    |[boolean](#boolean)|[between](#between)|[email](#email)|
|[nullable](#nullable)|[float](#float)    |[max](#max)        |[regex](#regex)|
|[required](#required)|[integer](#integer)|[min](#min)        |[url](#url)    |
|                     |[string](#string)  |                   |               |

## between:
Arguments: `<digit>,<digit>`
    
The constraint has different implementations based on the value type.
- If the value has a numeric constraint (integer or float), it must lie between the two values.
- Otherwise, the length of the value must be between the supplied values.

Example:
- string must have minimum length of 2 and maximum length of 6: `between:2,6`
- integer must have a value between 2 and 6 or less: `integer|between:2,6`

## boolean
The value must be bool or castable to bool.
- allowed `true` values: `1, '1', 'on', true`
- allowed `false` values: `0, 'off', '0', false`

## email
The value must be a valid email.

## filled
The value must be filled and not be null (except if [nullable](#nullable) is also set). If the value is an empty string, this validation rule fails.

## float
The value must be a float or castable to float.
- example of allowed values: `-1, 1, -1.1, 1.1, '1.1', '-1.1', '.1', '1.', '1', '-1'` 

## integer
The value must be an integer or castable to int.
- example of allowed values: `1, -1, '1', '-1'`

## max:
Argument: `<digit>`  
  
The constraint has different implementations based on the value type.
- If the value has a numeric constraint (integer or float), it must be smaller than the supplied value.
- Otherwise, the length of the value must be smaller than the supplied value.

Example:
 - string with maximum length of 6: `max:6`
 - integer which has to be 6 or less: `integer|max:6`

## min
Argument: `<digit>`  

The constraint has different implementations based on the value type.
- If the value has a numeric constraint (integer or float), it must be bigger than the supplied value.
- Otherwise, the length of the value must be bigger than the supplied value.

Example:
- string with minimum length of 6: `min:6`
- integer which has to be 6 or higher: `integer|min:6`

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

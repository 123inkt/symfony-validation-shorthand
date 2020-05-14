# Available shorthands
|General              |Type               |Range              |Pattern        |
|:--------------------|:------------------|:------------------|:--------------|
|[filled](#filled)    |[boolean](#boolean)|[between](#between)|[email](#email)|
|[nullable](#nullable)|[float](#float)    |[max](#max)        |[regex](#regex)|
|[required](#required)|[integer](#integer)|[min](#min)        |[url](#url)    |
|                     |[string](#string)  |                   |               |

## between
The constraint has different implementations based on the value type.
- If the value has a numeric constraint (integer or float), it must lie between the two values.
- Otherwise, the length of the value must be between the supplied values.

## boolean
The value must be a bool.

## email
The value must be a valid email.

## filled
The value must be filled and not be null (except if [nullable](#nullable) is also set). If the value is an empty string, this validation rule fails.

## float
The value must be a float.

## integer
The value must be an integer.

## max
The constraint has different implementations based on the value type.
- If the value has a numeric constraint (integer or float), it must smaller than the supplied value.
- Otherwise, the length of the value must be smaller than the supplied value.

## min
The constraint has different implementations based on the value type.
- If the value has a numeric constraint (integer or float), it must bigger than the supplied value.
- Otherwise, the length of the value must be bigger than the supplied value.

## nullable
The value can be ```null```.

## regex
The value must match the supplied regex.

## required
The value must be set and not be null (except if [nullable](#nullable) is also set). If the value is an empty string, this validation rule passes.

## string
The value must be a string.

## url
The value must be a valid url.

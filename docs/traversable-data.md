| Index                                             |
|:------------------------------------------------- |
| [Shorthands](available-shorthands.md)             |
| [Array data Validation](data-validation.md)       |  
| Traversable data validation                       | 

# Traversable data validation

Symfony's `All` Constraint allows to valid `\Traversable` data. For example data read from a csv or Excel provided via a yield function. Start
your notation with a `*`.

**Rules:**  
For a csv with 4 columns. 

```php
[
    '*.0' => 'required|int|min:1',
    '*.1' => 'required|string|email',
    '*.2' => 'required|float',
    '*.3' => 'required|string|max:2000'
]
```

**Validates:**
```php
$iterator = new ArrayIterator(
    [
        ['4', 'exampleA@example.com', '2.59', 'apples'],
        ['9', 'exampleB@example.com', '3.06', 'raspberries'],
        ['3', 'exampleC@example.com', '115.99', 'pineapple'],
    ]   
);
```

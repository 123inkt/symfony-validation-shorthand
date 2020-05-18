| Index                                             |
|:------------------------------------------------- |
| [Shorthands](available-shorthands.md)             |
| [Array data Validation](data-validation.md)       |  
| Traversable data validation                       |
| [Examples](examples.md)                           | 

# Traversable data validation

Symfony's `All` Constraint allows you to validate `\Traversable` data. For example data read from a csv or Excel provided via a yield function. The `*` 
notation marks the set as iterable and internally the `All` constraint will be used instead of `Collection`.

# ArrayIterator example

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

## Non empty value set
By default the `*` will allow the set to be empty. If you validate a set of values, you can mark the set as non-empty with the `required` rule.

**Rules:**  
```
['*' => 'required|int']
```

**Validates:**
```
success: [1, 2, '3']
fails:   []
fails:   [1, 'a']
```

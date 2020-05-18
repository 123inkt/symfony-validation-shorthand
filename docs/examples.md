| Index                                              |
|:---------------------------------------------------|
| [Shorthands](available-shorthands.md)              |
| [Array data Validation](data-validation.md)        |  
| [Traversable data validation](traversable-data.md) | 
| Examples                                           |

# Examples

### Required string
```
['name' => 'required|string']
```
```
success: ['name' => 'Peter Parker']
success: ['name' => '']
fails:   []
fails:   ['name' => null]
```

### Required nullable string
```
['name' => 'required|string|nullable']
```
```
success: ['name' => 'Peter Parker']
success: ['name' => '']
success: ['name' => null]
fails:   []
```

### Required nullable non-empty string
```
['name' => 'required|string|nullable|filled']
```
```
success: ['name' => 'Peter Parker']
success: ['name' => null]
fails:   ['name' => '']
fails:   []
```

### Optional nullable non-empty string
```
['name' => 'string|nullable|filled']
```
```
success: ['name' => 'Peter Parker']
success: ['name' => null]
success: []
fails:   ['name' => '']
```

### int array
```
['*' => 'int']
```
```
success: []
success: ['1', 2]
fails:   null
```

### non-empty int array
```
['*' => 'required|int']
```
```
success: ['1', 2]
fails:   []
fails:   null
```

### non-empty null or int array
```
['*' => 'required|int|null']
```
```
success: ['1', 2, null]
fails:   []
fails:   null
```

### custom constraints
```
['createdAt' => ['required|string', new \Symfony\Component\Validator\Constraints\Date()]
```
```
success: ['createdAt' => '2020-01-01']
fails:   [ 100 ]
fails:   []
```

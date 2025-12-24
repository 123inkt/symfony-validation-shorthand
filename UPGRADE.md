
# Upgrade guide

## From version 1.x to 2.0

Update all usages of `InConstraint`

**Before**
```php
new InConstraint(['values' => ['foo', 'bar']);
```

**After**
```php
new InConstraint(['foo', 'bar']);
```

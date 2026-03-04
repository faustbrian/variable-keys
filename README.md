[![GitHub Workflow Status][ico-tests]][link-tests]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

------

# variable-keys

Laravel Blueprint macros for variable primary key and morph types

## Requirements

> **Requires [PHP 8.4+](https://php.net/releases/)**

## Installation

```bash
composer require cline/variable-keys
```

## Usage

```php
use Cline\VariableKeys\Enums\PrimaryKeyType;
use Cline\VariableKeys\Enums\MorphType;

$primaryKeyType = PrimaryKeyType::tryFrom(config('database.primary_key_type'))
    ?? PrimaryKeyType::ID;

Schema::create('users', function (Blueprint $table) use ($primaryKeyType) {
    // Clean macro instead of verbose match expression
    $table->variablePrimaryKey($primaryKeyType);

    $table->string('name');
    $table->timestamps();
});

Schema::create('posts', function (Blueprint $table) use ($primaryKeyType) {
    $table->variablePrimaryKey($primaryKeyType);

    // Foreign key that matches the primary key type
    $table->variableForeignKey('user_id', $primaryKeyType)
          ->constrained()
          ->cascadeOnDelete();

    $table->string('title');
    $table->timestamps();
});
```

## Documentation

- **[Getting Started](https://docs.cline.sh/variable-keys/getting-started/)** - Installation and basic usage
- **[Primary Keys](https://docs.cline.sh/variable-keys/primary-keys/)** - Configure variable primary keys
- **[Foreign Keys](https://docs.cline.sh/variable-keys/foreign-keys/)** - Manage foreign key relationships
- **[Polymorphic Relations](https://docs.cline.sh/variable-keys/polymorphic-relations/)** - Handle polymorphic relationships
- **[Configuration Patterns](https://docs.cline.sh/variable-keys/configuration-patterns/)** - Centralize key type configuration
- **[API Reference](https://docs.cline.sh/variable-keys/api-reference/)** - Complete API documentation

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please use the [GitHub security reporting form][link-security] rather than the issue queue.

## Credits

- [Brian Faust][link-maintainer]
- [All Contributors][link-contributors]

## License

The MIT License. Please see [License File](LICENSE.md) for more information.

[ico-tests]: https://github.com/faustbrian/variable-keys/actions/workflows/quality-assurance.yaml/badge.svg
[ico-version]: https://img.shields.io/packagist/v/cline/variable-keys.svg
[ico-license]: https://img.shields.io/badge/License-MIT-green.svg
[ico-downloads]: https://img.shields.io/packagist/dt/cline/variable-keys.svg

[link-tests]: https://github.com/faustbrian/variable-keys/actions
[link-packagist]: https://packagist.org/packages/cline/variable-keys
[link-downloads]: https://packagist.org/packages/cline/variable-keys
[link-security]: https://github.com/faustbrian/variable-keys/security
[link-maintainer]: https://github.com/faustbrian
[link-contributors]: ../../contributors

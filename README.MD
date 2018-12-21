# Add possibility to map data from some external resource before updating or creating laravel models in fluent way

## Installation

You can install the package via composer:

```bash
composer require crazybooot/laravel-mapped-model-fields
```

## Usage
Implement `Crazybooot\MappedModelFields\Contracts\HasMappedFields` by adding `public function getFieldsMap(): array` method to your model class

```php
<?php
declare(strict_types = 1);

namespace App\Models;

use Crazybooot\MappedModelFields\Contracts\HasMappedFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Product
 *
 * @package App\Models
 */
class Product extends Model implements HasMappedFields
{
        /**
         * @return array
         */
        public function getFieldsMap(): array
        {
            return [
                'vendor_id'         => 'id',
                'title'             => 'title',
                'vendor_created_at' => 'created_at',
            ];
        }
}
```

After that you can create or update models with mapping from external source

```php
$externalProduct = [
    'id'        => 443765834,
    'title'     => 'Some awesome product',
    'crated_at' => '2018-12-21 14:36:04',
];

$product = Product::mapAndCreate($externalProduct);

$product->mapAndUpdate([
    'id'        => 855464545,
    'title'     => 'Some updated product title',
    'crated_at' => '2018-12-21 15:15:15',
]);

$user->products()->mapAndCreate($externalProduct);

Product::mapAndUpdateOrCreate(
    ['vendor_id' => $externalProduct['id']], 
    $externalProduct
);
```
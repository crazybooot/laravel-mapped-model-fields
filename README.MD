# Add possibility to map data from some external resource before updating or creating laravel models in fluent way

## Installation

You can install the package via composer:

```bash
composer require crazybooot/laravel-mapped-model-fields
```

## Usage
Implement `Crazybooot\MappedModelFields\Contracts\HasMappedFields` by adding `public function getFieldsMap(): array` method to your model class.
This method should return associative array, where key represents target property of current model, and value is string path to mapped value into source array.
You can use dot notation to map value from nested source array.

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
                'vendor_id'         => 'identifier',
                'title'             => 'information.title',
                'vendor_created_at' => 'created_at',
            ];
        }
}
```

After that you can create or update models with mapping from external source

```php
$externalProduct = [
    'identifier'        => 443765834,
    'information'     => [
        'title' => 'Some awesome product',
    ],
    'crated_at' => '2018-12-21 14:36:04',
];
//model method to map values from source and create entitie
$product = Product::mapAndCreate($externalProduct);

//relation method to map values from source and create related entitie
$product = $user->products()->mapAndCreate($externalProduct);

//model method to map values from source and update or create entitie
$product = Product::mapAndUpdateOrCreate(
    ['vendor_id' => $externalProduct['identifier']], 
    $externalProduct
);

//relation method to map values from source and update or create related entitie
$product = $user
    ->products()
    ->mapAndUpdateOrCreate(
        ['vendor_id' => $externalProduct['identifier']], 
        $externalProduct
    );
    
//model method to map values and update enitie
$product->mapAndUpdate($externalProduct);
```

Methods `mapAndUpdate` and `mapAndCreate` accepts array of values which will be append to attributes array after mapping as second argument,
and array with keys, which will be excluded from array of attributes after mapping as third argument.
`mapAndUpdateOrCreate` method accepts appends and excludes arrays as its third and fourth argument respectively. 

```php
$externalProduct = [
    'identifier'        => 443765834,
    'information'     => [
        'title' => 'Some awesome product',
    ],
    'crated_at' => '2018-12-21 14:36:04',
];
//append value to resulting array after mapping
$product = Product::mapAndCreate($externalProduct, ['user_id' => $user->getKey()]);
    
//axclude specified source value from mapping
$product->mapAndUpdate($externalProduct, [], ['information.title']);
```

You can specify transformation for mapped data:

```php
        /**
         * @return array
         */
        public function getFieldsMap(): array
        {
            return [
                'vendor_id'         => 'identifier',
                'title'             => [
                    'key'       => 'informaion.title',
                    'transform' => function($value) {
                        return str_upper($value);
                    }
                ],
                'vendor_created_at' => 'created_at',
            ];
        }
```
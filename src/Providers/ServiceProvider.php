<?php
declare(strict_types = 1);

namespace Crazybooot\MappedModelFields\Providers;

use Crazybooot\MappedModelFields\Contracts\HasMappedFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider
 *
 * @package Crazybooot\MappedModelFields\Providers
 */
class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $mapper = function (array $map, array $data, array $append = [], array $exclude = []) {
            $mappedFields = [];
            $map = empty($exclude) ? $map : \array_diff_key($map, \array_flip($exclude));

            foreach ($map as $k => $v) {
                if (\is_array($v) && isset($v['type'], $v['key'])) {
                    $value = \array_get($data, $v['key']);
                    \settype($value, $v['type']);
                    $mappedFields[$k] = $value;
                } else {
                    $mappedFields[$k] = \array_get($data, $v);
                }
            }

            return empty($append) ? $mappedFields : \array_merge($mappedFields, $append);
        };

        Builder::macro('mapAndCreate', function (array $attributes, array $append = [], array $exclude = []) use ($mapper) {
            $model = $this->getModel();

            $attributes = $model instanceof HasMappedFields
                ? $mapper($model->getFieldsMap(), $attributes, $append, $exclude)
                : $attributes;

            return $this->create($attributes);
        });


        Builder::macro('mapAndUpdate', function (array $attributes, array $append = [], array $exclude = []) use ($mapper) {
            $model = $this->getModel();

            $attributes = $model instanceof HasMappedFields
                ? $mapper($model->getFieldsMap(), $attributes, $append, $exclude)
                : $attributes;

            return $model->update($attributes);
        });

        Builder::macro('mapAndUpdateOrCreate', function (array $attributes, array $values = [], array $append = [], array $exclude = []) use ($mapper) {
            $model = $this->getModel();

            $values = $model instanceof HasMappedFields
                ? $mapper($model->getFieldsMap(), $values, $append, $exclude)
                : $values;

            return $this->updateOrCreate($attributes, $values);
        });

        Relation::macro('mapAndCreate', function (array $attributes, array $append = [], array $exclude = []) use ($mapper) {
            $model = $this->getQuery()->getModel();

            $attributes = $model instanceof HasMappedFields
                ? $mapper($model->getFieldsMap(), $attributes, $append, $exclude)
                : $attributes;

            return $this->create($attributes);
        });

        Relation::macro('mapAndUpdate', function (array $attributes, array $append = [], array $exclude = []) use ($mapper) {
            $model = $this->getQuery()->getModel();

            $attributes = $model instanceof HasMappedFields
                ? $mapper($model->getFieldsMap(), $attributes, $append, $exclude)
                : $attributes;

            return $model->update($attributes);
        });

        Relation::macro('mapAndUpdateOrCreate', function (array $attributes, array $values = [], array $append = [], array $exclude = []) use ($mapper) {
            $model = $this->getQuery()->getModel();

            $values = $model instanceof HasMappedFields
                ? $mapper($model->getFieldsMap(), $values, $append, $exclude)
                : $values;

            return $this->updateOrCreate($attributes, $values);
        });
    }
}
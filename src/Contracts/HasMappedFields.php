<?php
declare(strict_types = 1);

namespace Crazybooot\MappedModelFields\Contracts;

/**
 * Interface HasMappedFields
 *
 * @package Crazybooot\MappedModelFields\Contracts
 */
interface HasMappedFields
{
    /**
     * @return array
     */
    public function getFieldsMap(): array;
}
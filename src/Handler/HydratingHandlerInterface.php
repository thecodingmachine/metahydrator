<?php
namespace MetaHydrator\Handler;

use MetaHydrator\Exception\HydratingException;

/**
 * An interface handling partial extraction of raw data to target array.
 *
 * Interface HydratingHandlerInterface
 * @package MetaHhydrator\Handler
 */
interface HydratingHandlerInterface
{
    /**
     * @param array $data
     * @param array $targetData
     * @param $object
     * @return mixed
     *
     * @throws HydratingException
     */
    public function handle(array $data, array &$targetData, $object = null);
}

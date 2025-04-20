<?php

namespace App\Traits;

use Illuminate\Support\Facades\Redis;

trait RedisMethods
{
    /**
     * |-------------------------------------------------------------------------------
     * | REDIS METHODS - TRAIT
     * |-------------------------------------------------------------------------------
     * | Usage:
     * |  - Include this trait in Model which data need to be cached
     * |  - Use defined methods for: check, set and delete cache
     * |-------------------------------------------------------------------------------
     */

    /**
     * Get current model name and set name for cache
     *
     * @param int $id
     * @param string $suffix
     * @return string
     */
    private function getModelCacheKey(int $id, string $suffix = ''): string
    {
        $getModelNamespace = static::class;
        $getModelName = explode('\\', $getModelNamespace);
        $underscoredModelName = preg_replace('/([A-Z])/', '_$1', end($getModelName));
        $lowercaseModelName = strtolower(ltrim($underscoredModelName, '_'));

        return ($suffix != '')
            ? $lowercaseModelName . '_' . $suffix . '_' . $id
            : $lowercaseModelName . '_' . $id;
    }

    /**
     * Check if a cache exists
     *
     * @param int|null $id
     * @param string $suffix
     * @return mixed
     */
    public function checkCache(?int $id, string $suffix = ''): mixed
    {
        if (!$id) return null;

        return Redis::get($this->getModelCacheKey(id: $id, suffix: $suffix));
    }

    /**
     * Set cache
     *
     * @param int $id
     * @param mixed $data
     * @param string $suffix
     * @param float|int $cacheLifetime
     * @return void
     */
    public function setCache(int $id, mixed $data, string $suffix = '', float|int $cacheLifetime = 24 * 3600): void
    {
        Redis::set($this->getModelCacheKey(id: $id, suffix: $suffix), $data);
        Redis::expire($this->getModelCacheKey(id: $id, suffix: $suffix), $cacheLifetime);
    }

    /**
     * Delete cache
     *
     * @param int $id
     * @param string $suffix
     * @return void
     */
    public function deleteCache(int $id, string $suffix = ''): void
    {
        Redis::del($this->getModelCacheKey(id: $id, suffix: $suffix));
    }
}

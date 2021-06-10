<?php

namespace FSVendor\WPDesk\Persistence;

/**
 * Class MemoryContainer
 * @package WPDesk\Persistence
 */
class MemoryContainer implements \FSVendor\WPDesk\Persistence\PersistentContainer
{
    private $array;
    /**
     * Persist value for key
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->array[$key] = $value;
    }
    /**
     * Get persistent value for key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if (!isset($this->array[$key])) {
            throw new \FSVendor\WPDesk\Persistence\ElementNotExistsException(\sprintf('Element %s not exists!', $key));
        }
        return $this->array[$key];
    }
}

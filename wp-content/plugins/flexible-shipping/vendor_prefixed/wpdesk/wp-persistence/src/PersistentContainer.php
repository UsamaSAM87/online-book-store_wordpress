<?php

namespace FSVendor\WPDesk\Persistence;

interface PersistentContainer
{
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value);
    /**
     * @param string $key
     * @return mixed
     */
    public function get($key);
}

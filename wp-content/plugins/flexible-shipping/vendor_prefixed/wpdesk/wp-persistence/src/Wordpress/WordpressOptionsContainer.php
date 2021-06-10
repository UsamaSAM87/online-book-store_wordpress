<?php

namespace FSVendor\WPDesk\Persistence\Wordpress;

use FSVendor\WPDesk\Persistence\ElementNotExistsException;
use FSVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Class WordpressOptionsContainer
 * @package WPDesk\Persistence\Wordpress
 */
class WordpressOptionsContainer implements \FSVendor\WPDesk\Persistence\PersistentContainer
{
    const OPTION_PREFIX = 'saas-platform-client';
    /**
     * Set value.
     *
     * @param string $key Key.
     * @param mixed $value Value.
     */
    public function set($key, $value)
    {
        \update_option($this->prepareKeyName($key), $value);
    }
    /**
     * Prepare transient name for key.
     *
     * @param string $key Key.
     *
     * @return string
     */
    private function prepareKeyName($key)
    {
        return self::OPTION_PREFIX . '-' . $key;
    }
    /**
     * Get value.
     *
     * @param string $key Key.
     *
     * @return mixed
     * @throws ElementNotExistsException Element not found.
     */
    public function get($key)
    {
        $value = \get_option($this->prepareKeyName($key));
        if (\false === $value) {
            throw new \FSVendor\WPDesk\Persistence\ElementNotExistsException(\sprintf('Element %s not exists!', $key));
        }
        return $value;
    }
}

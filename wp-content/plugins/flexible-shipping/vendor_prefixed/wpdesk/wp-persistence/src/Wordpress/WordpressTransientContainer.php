<?php

namespace FSVendor\WPDesk\Persistence\Wordpress;

use FSVendor\WPDesk\Persistence\ElementNotExistsException;
use FSVendor\WPDesk\Persistence\PersistentContainer;
/**
 * Class WordpressTransientContainer
 * @package WPDesk\Persistence\Wordpress
 */
class WordpressTransientContainer implements \FSVendor\WPDesk\Persistence\PersistentContainer
{
    const TRANSIENT_NAME = 'wsp_';
    const TRANSIENT_TIMEOUT = 86400;
    /**
     * Set value.
     *
     * @param string $key Key.
     * @param mixed $value Value.
     */
    public function set($key, $value)
    {
        \set_transient($this->prepareTransientName($key), $value, self::TRANSIENT_TIMEOUT);
    }
    /**
     * Prepare transient name for key.
     *
     * @param string $key Key.
     *
     * @return string
     */
    private function prepareTransientName($key)
    {
        return self::TRANSIENT_NAME . \md5($key);
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
        $value = \get_transient($this->prepareTransientName($key));
        if (\false === $value) {
            throw new \FSVendor\WPDesk\Persistence\ElementNotExistsException(\sprintf('Element %s not exists!', $key));
        }
        return $value;
    }
}

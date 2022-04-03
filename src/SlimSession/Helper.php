<?php

namespace SlimSession;

/**
 * Helper class
 *
 * This is a general-purpose class that allows to manage PHP built-in sessions
 * and the session variables passed via $_SESSION superglobal.
 *
 * @package SlimSession
 * @author  Bryan Horna
 */
class Helper implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Get a session variable.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null): mixed
    {
        return $this->exists($key) ? $_SESSION[$key] : $default;
    }

    /**
     * Set a session variable.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set($key, $value): self
    {
        $_SESSION[$key] = $value;

        return $this;
    }

    /**
     * Merge values recursively.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function merge($key, $value): self
    {
        if (is_array($value) && is_array($old = $this->get($key))) {
            $value = array_merge_recursive($old, $value);
        }

        return $this->set($key, $value);
    }

    /**
     * Delete a session variable.
     *
     * @param string $key
     *
     * @return $this
     */
    public function delete($key): self
    {
        if ($this->exists($key)) {
            unset($_SESSION[$key]);
        }

        return $this;
    }

    /**
     * Clear all session variables.
     *
     * @return $this
     */
    public function clear(): self
    {
        $_SESSION = [];

        return $this;
    }

    /**
     * Check if a session variable is set.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists($key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    /**
     * Get or regenerate current session ID.
     *
     * @param bool $new
     *
     * @return string
     */
    public static function id($new = false): string
    {
        if ($new && session_id()) {
            session_regenerate_id(true);
        }

        return session_id() ?: '';
    }

    /**
     * Destroy the session.
     */
    public static function destroy()
    {
        if (self::id()) {
            session_unset();
            session_destroy();
            session_write_close();

            if (ini_get('session.use_cookies')) {
                Cookie::set(
                    session_name(),
                    '',
                    time() - 4200,
                    session_get_cookie_params()
                );
            }
        }
    }

    /**
     * Magic method for get.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key): mixed
    {
        return $this->get($key);
    }

    /**
     * Magic method for set.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Magic method for delete.
     *
     * @param string $key
     */
    public function __unset($key)
    {
        $this->delete($key);
    }

    /**
     * Magic method for exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function __isset($key): bool
    {
        return $this->exists($key);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count(): int
    {
        return count($_SESSION);
    }

    /**
     * Retrieve an external Iterator.
     *
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($_SESSION);
    }

    /**
     * Whether an array offset exists.
     *
     * @param mixed $offset
     *
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return $this->exists($offset);
    }

    /**
     * Retrieve value by offset.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Set a value by offset.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Remove a value by offset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }
}

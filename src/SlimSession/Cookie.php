<?php

namespace SlimSession;

/**
 * Cookie class
 *
 * This is an internal class that helps to handle SameSite cookie support in all
 * the supported PHP versions for this package in a standarized form.
 *
 * @package SlimSession
 * @author  Bryan Horna
 */
class Cookie
{
    /**
     * @param array      $params
     * @param array|null $set
     */
    private static function call($params, $set)
    {
        if ($set) {
            $expires = 'expires';
            $callback = 'setcookie';
        } else {
            $expires = 'lifetime';
            $callback = 'session_set_cookie_params';
        }

        $args = [
            $expires => $params[$expires],
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
        ];

        $new = PHP_VERSION_ID >= 70300;
        $samesite = @$params['samesite'];
        if ($new) {
            $args['samesite'] = $samesite;
            if ($set) {
                $args = [$set[0], $set[1], $args];
            } else {
                $args = [$args];
            }
        } else {
            if ($samesite) {
                $args['path'] .=
                    ($args['path'] ? '; ' : '') . "SameSite=$samesite";
            }
            $args = array_values($args);
            if ($set) {
                $args = array_merge([$set[0], $set[1]], $args);
            }
        }

        call_user_func_array($callback, $args);
    }

    /**
     * Set session cookie params.
     *
     * @param array $params
     */
    public static function setup($params)
    {
        self::call($params, null);
    }

    /**
     * Set a cookie.
     *
     * @param string $name
     * @param string $value
     * @param int    $expires
     * @param array  $params
     */
    public static function set($name, $value, $expires, $params)
    {
        $params['expires'] = $expires;
        self::call($params, [$name, $value]);
    }
}

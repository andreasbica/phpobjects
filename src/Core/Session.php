<?php

namespace PhpObjects\Core;

abstract class Session
{

    const DEFAULT_SESSION_ID_KEY = 'SESSID';
    const DEFAULT_GET_PARAM_RETURN_ON_FAIL = '__null__';
    const DEFAULT_SET_EXPIRE_ON_TIME = 15;
    const DEFAULT_IS_EXPIRED_DESTROY = true;
    const DEFAULT_IS_VALID_ID_WITH_ID_KEY = '';
    const DEFAULT_IS_VALID_ID_DESTROY_ON_FAIL = false;


    /**
     * @return bool
     */
    public static function start()
    {
        if (!static::hasId() || !$_SESSION) {
            return session_start();
        }
        return true;
    }

    /**
     * @return string
     */
    public static function getId()
    {
        if (!static::hasId()) {
            static::start();
        }
        return session_id();
    }

    /**
     * @return bool
     */
    public static function hasId()
    {
        return (session_id() ? true : false);
    }

    /**
     * @param string $keyName
     */
    public static function setIdKey($keyName)
    {
        static::setParam('__id_key__', $keyName);
    }

    /**
     * @return string Default: DEFAULT_SESSION_ID_KEY
     */
    public static function getIdKey()
    {
        return static::getParam('__id_key__', self::DEFAULT_SESSION_ID_KEY);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public static function setParam($key, $value)
    {
        $_SESSION[(string) $key] = $value;
    }

    /**
     * @param string $key
     * @param string $returnDefault Default: DEFAULT_GET_PARAM_RETURN_ON_FAIL ('__null__')
     * @return mixed
     */
    public static function getParam($key, $returnDefault = self::DEFAULT_GET_PARAM_RETURN_ON_FAIL)
    {
        $key = (string) $key;

        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }

        return $returnDefault;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function unsetParam($key)
    {
        $key = (string) $key;

        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
            return array_key_exists($key, $_SESSION);
        }

        return false;
    }

    /**
     * @param int $onTime Default: DEFAULT_SET_EXPIRE_ON_TIME (15 minutes)
     */
    public static function setExpire($onTime = self::DEFAULT_SET_EXPIRE_ON_TIME)
    {
        $_SESSION['__start__'] = time();
        $_SESSION['__expire__'] = $_SESSION['__start__'] + ($onTime * 60);
    }

    /**
     * @return bool
     */
    public static function refreshExpire()
    {
        if (static::hasExpire()) {
            $interval = $_SESSION['__expire__'] - $_SESSION['__start__'];

            $_SESSION['__start__'] = $_SESSION['__expire__'];
            $_SESSION['__expire__'] = $_SESSION['__start__'] + $interval;

            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public static function unsetExpire()
    {
        $isUnset = true;

        if (array_key_exists('__start__', $_SESSION)) {
            unset($_SESSION['__start__']);
            $isUnset &= !array_key_exists('__start__', $_SESSION);
        }

        if (array_key_exists('__expire__', $_SESSION)) {
            unset($_SESSION['__expire__']);
            $isUnset &= !array_key_exists('__expire__', $_SESSION);
        }

        return $isUnset;
    }

    /**
     * @return bool
     */
    public static function hasExpire()
    {
        if (array_key_exists('__start__', $_SESSION)) {
            return true;
        }
        return false;
    }

    /**
     * @param bool $destroy Default: DEFAULT_IS_EXPIRED_DESTROY (true)
     * @return bool
     */
    public static function isExpired($destroy = self::DEFAULT_IS_EXPIRED_DESTROY)
    {
        if (static::hasExpire() && time() > $_SESSION['__expire__']) {
            !$destroy ?: static::destroy();
            return true;
        }
        return false;
    }

    public static function destroy()
    {
        if ($_SESSION) {
            session_unset();
            session_destroy();
        }
    }

    /**
     * @param string $id
     * @param string $withIdKey Default: DEFAULT_IS_VALID_ID_WITH_ID_KEY ('')
     * @param bool $destroyOnFail Default: DEFAULT_IS_VALID_ID_DESTROY_ON_FAIL (false)
     * @return bool
     */
    public static function isValidId(
        $id,
        $withIdKey = self::DEFAULT_IS_VALID_ID_WITH_ID_KEY,
        $destroyOnFail = self::DEFAULT_IS_VALID_ID_DESTROY_ON_FAIL
    ) {
        if (static::hasId() && static::getId() === $id) {
            if (!empty($withIdKey)) {
                if ($withIdKey === static::getIdKey()) {
                    return true;
                }
                return false;
            }
            return true;
        }

        if ($destroyOnFail) {
            static::destroy();
        }

        return false;
    }

}
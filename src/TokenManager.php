<?php

namespace InternetPixels\CSRFProtection;

/**
 * Class TokenManager
 * @package InternetPixels\CSRFProtection
 */
class TokenManager
{

    const DAY_IN_SECONDS = 86400;

    /**
     * @var string
     */
    private static $salt = null;

    /**
     * @var int
     */
    private static $userId;

    /**
     * @var string
     */
    private static $sessionId;

    /**
     * Generate a new token for a user.
     *
     * @param string $name
     * @return string
     * @throws \Exception
     */
    public static function create(string $name)
    {
        if (empty($name)) {
            throw new \Exception('Set an action name for this token!');
        }

        if (empty(self::$userId)) {
            throw new \Exception('Set a user id in the TokenManager!');
        }

        if (empty(self::$salt)) {
            throw new \Exception('Set a salt in the TokenManager!');
        }

        if (empty(self::$sessionId)) {
            throw new \Exception('Set the session id in the TokenManager!');
        }

        $token = substr(self::hash(self::calculateValidation() . '|' . $name . '|' . self::$userId . '|' . self::$sessionId), -12, 10);

        return $token;
    }

    /**
     * Generate a new token and return a HTML hidden field for direct usage in forms.
     *
     * @param string $name
     * @param string $fieldName
     * @return string
     * @throws \Exception
     */
    public static function createHtmlField(string $name, string $fieldName = '_token')
    {
        $token = self::create($name);

        return sprintf('<input type="hidden" id="%s" name="%s" value="%s" />', $fieldName, $fieldName, $token);
    }

    /**
     * Validate a token.
     *
     * @param string $name
     * @param string $token
     * @return bool
     */
    public static function validate(string $name, string $token)
    {
        if (empty($token)) {
            return false;
        }

        // Nonce was generated 0-12 hours ago
        $expected = substr(self::hash(self::calculateValidation() . '|' . $name . '|' . self::$userId . '|' . self::$sessionId), -12, 10);

        if (hash_equals($expected, $token)) {
            return true;
        }

        // Nonce was generated 12-24 hours ago
        $expected = substr(self::hash((self::calculateValidation() - 1) . '|' . $name . '|' . self::$userId . '|' . self::$sessionId), -12, 10);

        if (hash_equals($expected, $token)) {
            return true;
        }

        return false;
    }

    /**
     * Set the salt for hashing.
     *
     * @param string $salt
     * @param string $key
     */
    public static function setSalt(string $salt, string $key)
    {
        self::$salt = hash_hmac('md5', 'nonce', $key);
    }

    /**
     * Set the user id for hashing.
     *
     * @param int $userId
     */
    public static function setUserId(int $userId)
    {
        self::$userId = $userId;
    }

    /**
     * Set the session id for hashing.
     *
     * @param string $sessionId
     */
    public static function setSessionToken(string $sessionId)
    {
        self::$sessionId = hash_hmac('sha256', $sessionId, 'token');
    }

    /**
     * Generate a md5 hash using the salt.
     *
     * @param string $value
     * @return string
     */
    private static function hash(string $value)
    {
        return hash_hmac('md5', $value, self::$salt);
    }

    /**
     * Calculate the validation period
     *
     * @return int
     */
    private static function calculateValidation()
    {
        return ceil(time() / (self::DAY_IN_SECONDS / 2));
    }

}
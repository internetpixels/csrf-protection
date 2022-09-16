<?php

namespace InternetPixels\CSRFProtection\Tests;

use InternetPixels\CSRFProtection\TokenManager;
use PHPUnit\Framework\TestCase;
use Exception;

/**
 * Class TokenManagerTest
 * @package InternetPixels\CSRFProtection\Tests
 */
class TokenManagerTest extends TestCase
{
    private string $testSalt = 'P*17OJznMttaR#Zzwi4YhAY!H7hPGUCd';
    private string $testKey = 'ERGirehgr4893ur43tjrg98rut98ueowifj';

    /**
     * @var string SHA256 session token
     */
    private string $testSessionToken = 'session_token';

    public function testEmptySalt(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Set a user id in the TokenManager!');

        TokenManager::create('test');
    }

    public function testEmptySaltWithuserId(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Set a salt in the TokenManager!');

        TokenManager::setUserId(7);
        TokenManager::create('test');
    }

    public function testEmptySaltWithemptyName(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Set an action name for this token!');

        TokenManager::setUserId(7);
        TokenManager::create('');
    }

    public function testEmptySaltWithemptySessionToken(): void
    {
        self::expectException(Exception::class);
        self::expectExceptionMessage('Set the session id in the TokenManager!');

        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);

        TokenManager::create('test_action');
    }

    public function testCreateToken(): void
    {
        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);
        TokenManager::setSessionToken($this->testSessionToken);

        $token = TokenManager::create('test_action');

        $this->assertEquals(10, strlen($token));
    }

    public function testCreateTokensWithsleep(): void
    {
        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);
        TokenManager::setSessionToken($this->testSessionToken);

        $tokens = [];
        for ($i = 1; $i <= 5; $i++) {
            $tokens[$i] = TokenManager::create('test_action_' . $i);
            usleep(450000);
        }

        foreach ($tokens as $key => $token) {
            $this->assertTrue(TokenManager::validate('test_action_' . $key, $token));
        }
    }

    public function testValidateToken(): void
    {
        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);
        TokenManager::setSessionToken($this->testSessionToken);

        $token = TokenManager::create('test_action');

        $this->assertTrue(TokenManager::validate('test_action', $token));
    }

    public function testHtmlField(): void
    {
        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);
        TokenManager::setSessionToken($this->testSessionToken);

        $html = TokenManager::createHtmlField('test_action');

        $this->assertStringStartsWith('<input type="hidden" id="_token" name="_token" value="', $html);
        $this->assertStringEndsWith('" />', $html);
    }

    public function testHtmlFieldValidateToken(): void
    {
        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);
        TokenManager::setSessionToken($this->testSessionToken);

        $html = TokenManager::createHtmlField('test_action');
        preg_match('/value="(.*)"/i', $html, $matches);

        $this->assertEquals(10, strlen($matches[1]));
        $this->assertTrue(TokenManager::validate('test_action', $matches[1]));
    }

    public function testHtmlFieldWithcustomFieldName(): void
    {
        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);
        TokenManager::setSessionToken($this->testSessionToken);

        $html = TokenManager::createHtmlField('test_action', 'my_field');

        $this->assertStringStartsWith('<input type="hidden" id="my_field" name="my_field" value="', $html);
        $this->assertStringEndsWith('" />', $html);
    }
}

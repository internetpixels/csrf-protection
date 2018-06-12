<?php

namespace InternetPixels\CSRFProtection\Tests;

use InternetPixels\CSRFProtection\TokenManager;
use PHPUnit\Framework\TestCase;

/**
 * Class TokenManagerTest
 * @package InternetPixels\CSRFProtection\Tests
 */
class TokenManagerTest extends TestCase
{

    private $testSalt = 'P*17OJznMttaR#Zzwi4YhAY!H7hPGUCd';

    private $testKey = 'ERGirehgr4893ur43tjrg98rut98ueowifj';

    /**
     * @var string SHA256 session token
     */
    private $testSessionToken = 'session_token';

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Set a user id in the TokenManager!
     */
    public function testEmptySalt()
    {
        TokenManager::create('test');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Set a salt in the TokenManager!
     */
    public function testEmptySalt_WITH_userId()
    {
        TokenManager::setUserId(7);
        TokenManager::create('test');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Set an action name for this token!
     */
    public function testEmptySalt_WITH_emptyName()
    {
        TokenManager::setUserId(7);
        TokenManager::create('');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Set the session id in the TokenManager!
     */
    public function testEmptySalt_WITH_emptySessionToken()
    {
        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);

        TokenManager::create('test_action');
    }

    public function testCreateToken()
    {
        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);
        TokenManager::setSessionToken($this->testSessionToken);

        $token = TokenManager::create('test_action');

        $this->assertEquals(10, strlen($token));
    }

    public function testCreateTokens_WITH_sleep()
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

    public function testValidateToken()
    {
        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);
        TokenManager::setSessionToken($this->testSessionToken);

        $token = TokenManager::create('test_action');

        $this->assertTrue(TokenManager::validate('test_action', $token));
    }

    public function testHtmlField()
    {
        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);
        TokenManager::setSessionToken($this->testSessionToken);

        $html = TokenManager::createHtmlField('test_action');

        $this->assertStringStartsWith('<input type="hidden" id="_token" name="_token" value="', $html);
        $this->assertStringEndsWith('" />', $html);
    }

    public function testHtmlFieldValidateToken()
    {
        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);
        TokenManager::setSessionToken($this->testSessionToken);

        $html = TokenManager::createHtmlField('test_action');
        preg_match('/value="(.*)"/i', $html, $matches);

        $this->assertEquals(10, strlen($matches[1]));
        $this->assertTrue(TokenManager::validate('test_action', $matches[1]));
    }

    public function testHtmlField_WITH_customFieldName()
    {
        TokenManager::setSalt($this->testSalt, $this->testKey);
        TokenManager::setUserId(7);
        TokenManager::setSessionToken($this->testSessionToken);

        $html = TokenManager::createHtmlField('test_action', 'my_field');

        $this->assertStringStartsWith('<input type="hidden" id="my_field" name="my_field" value="', $html);
        $this->assertStringEndsWith('" />', $html);
    }

}
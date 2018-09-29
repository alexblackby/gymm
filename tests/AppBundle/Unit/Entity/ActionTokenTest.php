<?php

namespace Tests\AppBundle\Unit\Entity;

use AppBundle\Entity\ActionToken;
use PHPUnit\Framework\TestCase;


class ActionTokenTest extends TestCase
{
    public function testExpiration()
    {
        $type = "test";

        $tokenForOneDay = new ActionToken($type, 1);
        $tokenExpired = new ActionToken($type, 0);

        $this->assertTrue($tokenForOneDay->validate($tokenForOneDay->getSecret(), $type));
        $this->assertFalse($tokenExpired->validate($tokenExpired->getSecret(), $type));
    }


    public function testTypeValidation()
    {
        $type = "test";
        $badType = "bad_type";

        $token = new ActionToken($type);
        $secret = $token->getSecret();


        $this->assertTrue($token->validate($secret, $type));
        $this->assertFalse($token->validate($secret, $badType));
    }


    public function testTokenStringValidation()
    {
        $type = "test";
        $token = new ActionToken($type);
        $this->setPrivateProperty($token, 'id', 1);

        $tokenString = $token->getTokenString();
        $tokenParams = ActionToken::parseTokenString($tokenString);

        $this->assertTrue($token->validate($tokenParams['secret'], $type));
        $this->assertFalse($token->validate("bad_secret", $type));
    }


    private function setPrivateProperty($object, $property, $value)
    {
        $reflectionClass = new \ReflectionClass(get_class($object));
        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }
}
<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../utils/password.class.php';

class PasswordTest extends TestCase
{
    public function testHashAndCheckReturnTrue()
    {
        $password = new \utils\password();
        $plain = 'secret123';
        $hash = $password->getHashToStore($plain);
        $this->assertTrue($password->checkPassword($plain, $hash));
    }
}

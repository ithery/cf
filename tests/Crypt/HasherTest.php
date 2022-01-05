<?php

use PHPUnit\Framework\TestCase;

class HasherTest extends TestCase {
    public function testBasicBcryptHashing() {
        $hasher = new CCrypt_Hasher_BcryptHasher();
        $value = $hasher->make('password');
        $this->assertNotSame('password', $value);
        $this->assertTrue($hasher->check('password', $value));
        $this->assertFalse($hasher->needsRehash($value));
        $this->assertTrue($hasher->needsRehash($value, ['rounds' => 1]));
        $this->assertSame('bcrypt', password_get_info($value)['algoName']);
    }

    public function testBasicArgon2iHashing() {
        $hasher = new CCrypt_Hasher_ArgonHasher();
        $value = $hasher->make('password');
        $this->assertNotSame('password', $value);
        $this->assertTrue($hasher->check('password', $value));
        $this->assertFalse($hasher->needsRehash($value));
        $this->assertTrue($hasher->needsRehash($value, ['threads' => 1]));
        $this->assertSame('argon2i', password_get_info($value)['algoName']);
    }

    public function testBasicArgon2idHashing() {
        $hasher = new CCrypt_Hasher_Argon2IdHasher();
        $value = $hasher->make('password');
        $this->assertNotSame('password', $value);
        $this->assertTrue($hasher->check('password', $value));
        $this->assertFalse($hasher->needsRehash($value));
        $this->assertTrue($hasher->needsRehash($value, ['threads' => 1]));
        $this->assertSame('argon2id', password_get_info($value)['algoName']);
    }

    /**
     * @depends testBasicBcryptHashing
     */
    public function testBasicBcryptVerification() {
        $this->expectException(RuntimeException::class);

        $argonHasher = new CCrypt_Hasher_ArgonHasher(['verify' => true]);
        $argonHashed = $argonHasher->make('password');
        (new CCrypt_Hasher_BcryptHasher(['verify' => true]))->check('password', $argonHashed);
    }

    /**
     * @depends testBasicArgon2iHashing
     */
    public function testBasicArgon2iVerification() {
        $this->expectException(RuntimeException::class);

        $bcryptHasher = new CCrypt_Hasher_BcryptHasher(['verify' => true]);
        $bcryptHashed = $bcryptHasher->make('password');
        (new CCrypt_Hasher_ArgonHasher(['verify' => true]))->check('password', $bcryptHashed);
    }

    /**
     * @depends testBasicArgon2idHashing
     */
    public function testBasicArgon2idVerification() {
        $this->expectException(RuntimeException::class);

        $bcryptHasher = new CCrypt_Hasher_BcryptHasher(['verify' => true]);
        $bcryptHashed = $bcryptHasher->make('password');
        (new CCrypt_Hasher_Argon2IdHasher(['verify' => true]))->check('password', $bcryptHashed);
    }
}

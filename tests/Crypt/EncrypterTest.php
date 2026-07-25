<?php

use PHPUnit\Framework\TestCase;

class EncrypterTest extends TestCase {
    public function testEncryption() {
        $e = new CCrypt_Encrypter(str_repeat('a', 16));
        $encrypted = $e->encrypt('foo');
        $this->assertNotSame('foo', $encrypted);
        $this->assertSame('foo', $e->decrypt($encrypted));
    }

    public function testEncryptionUsesAes256CbcCipherByDefaultConstructorArg() {
        $e = new CCrypt_Encrypter(str_repeat('a', 32), 'aes-256-cbc');
        $encrypted = $e->encrypt('bar');
        $this->assertNotSame('bar', $encrypted);
        $this->assertSame('bar', $e->decrypt($encrypted));
    }

    public function testWithCustomCipher() {
        $e = new CCrypt_Encrypter(str_repeat('b', 32), 'aes-256-cbc');
        $encrypted = $e->encrypt('bar');
        $this->assertNotSame('bar', $encrypted);
        $this->assertSame('bar', $e->decrypt($encrypted));

        $e = new CCrypt_Encrypter(random_bytes(32), 'aes-256-cbc');
        $encrypted = $e->encrypt('foo');
        $this->assertNotSame('foo', $encrypted);
        $this->assertSame('foo', $e->decrypt($encrypted));
    }

    public function testDoNoAllowLongerKey() {
        $this->expectException(RuntimeException::class);

        new CCrypt_Encrypter(str_repeat('z', 32));
    }

    public function testWithBadKeyLength() {
        $this->expectException(RuntimeException::class);

        new CCrypt_Encrypter(str_repeat('a', 5));
    }

    public function testWithBadKeyLengthAlternativeCipher() {
        $this->expectException(RuntimeException::class);

        new CCrypt_Encrypter(str_repeat('a', 16), 'aes-256-cbc');
    }

    public function testWithUnsupportedCipher() {
        $this->expectException(RuntimeException::class);

        new CCrypt_Encrypter(str_repeat('c', 16), 'aes-128-cfb8');
    }

    public function testSupportedMethodAcceptsValidKeyCipherCombinations() {
        $this->assertTrue(CCrypt_Encrypter::supported(str_repeat('a', 16), 'aes-128-cbc'));
        $this->assertTrue(CCrypt_Encrypter::supported(str_repeat('a', 32), 'aes-256-cbc'));
        $this->assertTrue(CCrypt_Encrypter::supported(str_repeat('a', 16), 'aes-128-gcm'));
        $this->assertTrue(CCrypt_Encrypter::supported(str_repeat('a', 32), 'aes-256-gcm'));
    }

    public function testSupportedMethodRejectsInvalidKeyLength() {
        $this->assertFalse(CCrypt_Encrypter::supported(str_repeat('a', 5), 'aes-128-cbc'));
        $this->assertFalse(CCrypt_Encrypter::supported(str_repeat('a', 31), 'aes-256-cbc'));
    }

    public function testSupportedMethodRejectsUnknownCipher() {
        $this->assertFalse(CCrypt_Encrypter::supported(str_repeat('a', 16), 'aes-128-cfb8'));
    }

    public function testSupportedMethodIsCaseInsensitiveForCipher() {
        $this->assertTrue(CCrypt_Encrypter::supported(str_repeat('a', 16), 'AES-128-CBC'));
    }

    public function testGenerateKeyCreatesKeyOfCorrectLengthForCipher() {
        $key = CCrypt_Encrypter::generateKey('aes-128-cbc');
        $this->assertSame(16, mb_strlen($key, '8bit'));

        $key = CCrypt_Encrypter::generateKey('aes-256-cbc');
        $this->assertSame(32, mb_strlen($key, '8bit'));

        $key = CCrypt_Encrypter::generateKey('aes-128-gcm');
        $this->assertSame(16, mb_strlen($key, '8bit'));

        $key = CCrypt_Encrypter::generateKey('aes-256-gcm');
        $this->assertSame(32, mb_strlen($key, '8bit'));
    }

    public function testGeneratedKeyIsUsableForConstructingAnEncrypter() {
        $cipher = 'aes-256-cbc';
        $key = CCrypt_Encrypter::generateKey($cipher);
        $e = new CCrypt_Encrypter($key, $cipher);

        $encrypted = $e->encrypt('foo');
        $this->assertSame('foo', $e->decrypt($encrypted));
    }

    public function testEncryptedLengthIsFixed() {
        $e = new CCrypt_Encrypter(str_repeat('a', 16));
        $lengths = [];
        for ($i = 0; $i < 3; ++$i) {
            $lengths[] = strlen($e->encrypt('foo'));
        }
        $this->assertSame($lengths[0], $lengths[1]);
        $this->assertSame($lengths[1], $lengths[2]);
    }

    public function testExceptionThrownWhenPayloadIsInvalid() {
        $this->expectException(CCrypt_Exception_DecryptException::class);
        $this->expectExceptionMessage('The payload is invalid.');

        $e = new CCrypt_Encrypter(str_repeat('a', 16));
        $payload = $e->encrypt('foo');
        $payload = str_shuffle($payload);
        $e->decrypt($payload);
    }

    public function testExceptionThrownWhenPayloadIsNotValidJson() {
        $this->expectException(CCrypt_Exception_DecryptException::class);
        $this->expectExceptionMessage('The payload is invalid.');

        $e = new CCrypt_Encrypter(str_repeat('a', 16));
        $payload = base64_encode('not-valid-json');
        $e->decrypt($payload);
    }

    public function testExceptionThrownWithDifferentKey() {
        $this->expectException(CCrypt_Exception_DecryptException::class);
        $this->expectExceptionMessage('The MAC is invalid.');

        $a = new CCrypt_Encrypter(str_repeat('a', 16));
        $b = new CCrypt_Encrypter(str_repeat('b', 16));

        $payload = $a->encrypt('foo');
        $b->decrypt($payload);
    }

    public function testExceptionThrownWhenIvIsTamperedWith() {
        $this->expectException(CCrypt_Exception_DecryptException::class);

        $e = new CCrypt_Encrypter(str_repeat('a', 16));
        $payload = json_decode(base64_decode($e->encrypt('foo')), true);
        $payload['iv'] = base64_encode(str_repeat('x', strlen(base64_decode($payload['iv']))));
        $modified = base64_encode(json_encode($payload));
        $e->decrypt($modified);
    }

    public function testExceptionThrownWhenValueIsTamperedWith() {
        $this->expectException(CCrypt_Exception_DecryptException::class);
        $this->expectExceptionMessage('The MAC is invalid.');

        $e = new CCrypt_Encrypter(str_repeat('a', 16));
        $payload = json_decode(base64_decode($e->encrypt('foo')), true);
        $payload['value'] = base64_encode('tampered-value-here');
        $modified = base64_encode(json_encode($payload));
        $e->decrypt($modified);
    }

    public function testExceptionThrownWhenMacIsTamperedWith() {
        $this->expectException(CCrypt_Exception_DecryptException::class);
        $this->expectExceptionMessage('The MAC is invalid.');

        $e = new CCrypt_Encrypter(str_repeat('a', 16));
        $payload = json_decode(base64_decode($e->encrypt('foo')), true);
        $payload['mac'] = str_repeat('0', strlen($payload['mac']));
        $modified = base64_encode(json_encode($payload));
        $e->decrypt($modified);
    }

    public function testEncryptStringDoesNotSerializeValue() {
        $e = new CCrypt_Encrypter(str_repeat('a', 16));
        $encrypted = $e->encryptString('foo');
        $this->assertSame('foo', $e->decryptString($encrypted));
    }

    public function testEncryptStringVsEncryptWithSerializeFalse() {
        $e = new CCrypt_Encrypter(str_repeat('a', 16));

        $encrypted1 = $e->encryptString('foo');
        $encrypted2 = $e->encrypt('foo', false);

        $this->assertSame('foo', $e->decrypt($encrypted1, false));
        $this->assertSame('foo', $e->decrypt($encrypted2, false));
        $this->assertSame('foo', $e->decryptString($encrypted2));
    }

    public function testDecryptStringOfSerializedPayloadReturnsRawSerializedForm() {
        // decryptString() passes unserialize=false, so decrypting a payload
        // that was encrypt()-ed with serialization on returns the raw
        // serialized PHP representation instead of throwing.
        $e = new CCrypt_Encrypter(str_repeat('a', 16));
        $encrypted = $e->encrypt('foo', true);
        $this->assertSame(serialize('foo'), $e->decryptString($encrypted));
    }

    public function testEncryptAndDecryptArraysAndObjectsWithSerialization() {
        $e = new CCrypt_Encrypter(str_repeat('a', 16));

        $array = ['foo' => 'bar', 'baz' => [1, 2, 3]];
        $encrypted = $e->encrypt($array);
        $this->assertEquals($array, $e->decrypt($encrypted));

        $object = new stdClass();
        $object->foo = 'bar';
        $encryptedObject = $e->encrypt($object);
        $decryptedObject = $e->decrypt($encryptedObject);
        $this->assertEquals($object, $decryptedObject);
    }

    public function testWithoutSerializationDoesNotUnserializeAScalarValue() {
        $e = new CCrypt_Encrypter(str_repeat('a', 16));
        $encrypted = $e->encrypt('foo', false);
        $this->assertSame('foo', $e->decrypt($encrypted, false));
    }

    public function testGetKeyReturnsTheKeyUsedByTheEncrypter() {
        $key = str_repeat('a', 16);
        $e = new CCrypt_Encrypter($key);
        $this->assertSame($key, $e->getKey());
    }

    public function testGcmCipherEncryptionAndDecryption() {
        $e = new CCrypt_Encrypter(str_repeat('a', 16), 'aes-128-gcm');
        $encrypted = $e->encrypt('foo');
        $this->assertNotSame('foo', $encrypted);
        $this->assertSame('foo', $e->decrypt($encrypted));
    }

    public function testGcmCipherTamperedTagFails() {
        $this->expectException(CCrypt_Exception_DecryptException::class);

        $e = new CCrypt_Encrypter(str_repeat('a', 16), 'aes-128-gcm');
        $payload = json_decode(base64_decode($e->encrypt('foo')), true);
        $payload['tag'] = base64_encode(str_repeat('x', 16));
        $modified = base64_encode(json_encode($payload));
        $e->decrypt($modified);
    }
}

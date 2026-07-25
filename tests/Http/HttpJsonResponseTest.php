<?php

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use PHPUnit\Framework\TestCase;

/**
 * Tests for CHTTP_JsonResponse (system/libraries/CHTTP/JsonResponse.php).
 */
class HttpJsonResponseTest extends TestCase {
    public function testConstructorDefaultsToEmptyObject() {
        $response = new CHTTP_JsonResponse();

        // Symfony's base JsonResponse converts a null $data into an ArrayObject,
        // which encodes as an empty JSON object rather than an empty array.
        $this->assertSame('{}', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testConstructorWithArrayData() {
        $response = new CHTTP_JsonResponse(['a' => 1], 201);

        $this->assertSame(json_encode(['a' => 1]), $response->getContent());
        $this->assertSame(201, $response->getStatusCode());
    }

    public function testSetDataArray() {
        $response = new CHTTP_JsonResponse();
        $result = $response->setData(['foo' => 'bar']);

        $this->assertSame($response, $result);
        $this->assertSame(json_encode(['foo' => 'bar']), $response->getContent());
        $this->assertSame(['foo' => 'bar'], $response->original);
    }

    public function testSetDataJsonable() {
        $jsonable = new class implements Jsonable {
            public function toJson($options = 0) {
                return '{"custom":1}';
            }
        };

        $response = new CHTTP_JsonResponse();
        $response->setData($jsonable);

        $this->assertSame('{"custom":1}', $response->getContent());
    }

    public function testSetDataJsonSerializable() {
        $serializable = new class implements JsonSerializable {
            #[\ReturnTypeWillChange]
            public function jsonSerialize() {
                return ['ser' => true];
            }
        };

        $response = new CHTTP_JsonResponse();
        $response->setData($serializable);

        $this->assertSame(json_encode(['ser' => true]), $response->getContent());
    }

    public function testSetDataArrayable() {
        $arrayable = new class implements Arrayable {
            public function toArray() {
                return ['arr' => true];
            }
        };

        $response = new CHTTP_JsonResponse();
        $response->setData($arrayable);

        $this->assertSame(json_encode(['arr' => true]), $response->getContent());
    }

    public function testSetDataThrowsOnEncodingError() {
        $response = new CHTTP_JsonResponse();

        $this->expectException(InvalidArgumentException::class);

        // NAN cannot be represented in JSON and JSON_PARTIAL_OUTPUT_ON_ERROR is not set,
        // so encoding must fail and hasValidJson() should surface the error.
        $response->setData(['bad' => NAN]);
    }

    public function testSetDataWithPartialOutputOnErrorOptionSuppressesException() {
        $response = new CHTTP_JsonResponse();
        $response->setEncodingOptions(JSON_PARTIAL_OUTPUT_ON_ERROR);

        // Should not throw because the option tells the response to tolerate the error,
        // substituting 0 for the unencodable NAN value instead.
        $response->setData(['bad' => NAN]);

        $this->assertSame('{"bad":0}', $response->getContent());
    }

    public function testGetDataReturnsObjectByDefault() {
        $response = new CHTTP_JsonResponse(['foo' => 'bar']);

        $data = $response->getData();

        $this->assertIsObject($data);
        $this->assertSame('bar', $data->foo);
    }

    public function testGetDataAssoc() {
        $response = new CHTTP_JsonResponse(['foo' => 'bar']);

        $data = $response->getData(true);

        $this->assertSame(['foo' => 'bar'], $data);
    }

    public function testWithCallbackWrapsContentAsJsonp() {
        $response = new CHTTP_JsonResponse(['a' => 1]);
        $result = $response->withCallback('myCallback');

        $this->assertSame($response, $result);
        // Symfony's setCallback() prefixes a "/**/" comment for XSSI protection.
        $this->assertSame('/**/myCallback(' . json_encode(['a' => 1]) . ');', $response->getContent());
        $this->assertSame('text/javascript', $response->headers->get('Content-Type'));
    }

    public function testWithCallbackNullRestoresJson() {
        $response = new CHTTP_JsonResponse(['a' => 1]);
        $response->withCallback('myCallback');
        $response->withCallback(null);

        $this->assertSame(json_encode(['a' => 1]), $response->getContent());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testSetEncodingOptionsReEncodesData() {
        $response = new CHTTP_JsonResponse(['path' => 'a/b']);
        // Default options escape slashes.
        $this->assertSame('{"path":"a\/b"}', $response->getContent());

        $response->setEncodingOptions(JSON_UNESCAPED_SLASHES);

        $this->assertSame('{"path":"a/b"}', $response->getContent());
    }

    public function testHasEncodingOption() {
        $response = new CHTTP_JsonResponse();
        $response->setEncodingOptions(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $this->assertTrue($response->hasEncodingOption(JSON_PRETTY_PRINT));
        $this->assertTrue($response->hasEncodingOption(JSON_UNESCAPED_SLASHES));
        $this->assertFalse($response->hasEncodingOption(JSON_UNESCAPED_UNICODE));
    }

    public function testGetCallbackReturnsNullByDefault() {
        $response = new CHTTP_JsonResponse();

        $this->assertNull($response->getCallback());
    }

    public function testHeaderAndWithHeadersFromResponseTrait() {
        $response = new CHTTP_JsonResponse();
        $response->header('X-Foo', 'bar');
        $response->withHeaders(['X-Baz' => 'qux']);

        $this->assertSame('bar', $response->headers->get('X-Foo'));
        $this->assertSame('qux', $response->headers->get('X-Baz'));
    }

    public function testWithCookieAddsCookie() {
        $cookie = new Symfony\Component\HttpFoundation\Cookie('jn', 'jv');

        $response = new CHTTP_JsonResponse();
        $response->withCookie($cookie);

        $this->assertSame('jn', $response->headers->getCookies()[0]->getName());
    }
}

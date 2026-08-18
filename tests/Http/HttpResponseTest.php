<?php

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use PHPUnit\Framework\TestCase;

/**
 * Tests for CHTTP_Response (system/libraries/CHTTP/Response.php)
 * and the shared CHTTP_Trait_ResponseTrait behavior it consumes.
 */
class HttpResponseTest extends TestCase {
    public function testConstructorSetsContentStatusAndProtocolVersion() {
        $response = new CHTTP_Response('hello world', 201, ['X-Foo' => 'bar']);

        $this->assertSame('hello world', $response->getContent());
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('bar', $response->headers->get('X-Foo'));
        $this->assertSame('1.0', $response->getProtocolVersion());
    }

    public function testConstructorDefaults() {
        $response = new CHTTP_Response();

        $this->assertSame('', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testSetContentPlainString() {
        $response = new CHTTP_Response();
        $response->setContent('plain text');

        $this->assertSame('plain text', $response->getContent());
        $this->assertSame('plain text', $response->getOriginalContent());
        $this->assertFalse($response->headers->has('Content-Type') && $response->headers->get('Content-Type') === 'application/json');
    }

    public function testSetContentArrayIsJsonEncoded() {
        $response = new CHTTP_Response();
        $response->setContent(['a' => 1, 'b' => 2]);

        $this->assertSame(json_encode(['a' => 1, 'b' => 2]), $response->getContent());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        // Original content should be preserved as the raw array, not the JSON string.
        $this->assertSame(['a' => 1, 'b' => 2], $response->getOriginalContent());
    }

    public function testSetContentArrayable() {
        $arrayable = new class implements Arrayable {
            public function toArray() {
                return ['x' => 1];
            }
        };

        $response = new CHTTP_Response();
        $response->setContent($arrayable);

        $this->assertSame(json_encode(['x' => 1]), $response->getContent());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertSame($arrayable, $response->getOriginalContent());
    }

    public function testSetContentJsonable() {
        $jsonable = new class implements Jsonable {
            public function toJson($options = 0) {
                return '{"custom":true}';
            }
        };

        $response = new CHTTP_Response();
        $response->setContent($jsonable);

        $this->assertSame('{"custom":true}', $response->getContent());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testSetContentJsonSerializable() {
        $serializable = new class implements JsonSerializable {
            #[\ReturnTypeWillChange]
            public function jsonSerialize() {
                return ['serialized' => true];
            }
        };

        $response = new CHTTP_Response();
        $response->setContent($serializable);

        $this->assertSame(json_encode(['serialized' => true]), $response->getContent());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testSetContentArrayObject() {
        $arrayObject = new ArrayObject(['k' => 'v']);

        $response = new CHTTP_Response();
        $response->setContent($arrayObject);

        $this->assertSame(json_encode($arrayObject), $response->getContent());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testSetContentRenderable() {
        $renderable = new class implements Renderable {
            public function render() {
                return '<p>rendered</p>';
            }
        };

        $response = new CHTTP_Response();
        $response->setContent($renderable);

        $this->assertSame('<p>rendered</p>', $response->getContent());
        // Renderable content is not JSON, so no Content-Type should be forced.
        $this->assertNotSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testGetOriginalContentUnwrapsNestedResponse() {
        $inner = new CHTTP_Response('inner content');
        $outer = new CHTTP_Response('outer content');
        // Manually simulate an "original" that is itself a response instance, exercising
        // the recursive branch of getOriginalContent().
        $outer->original = $inner;

        $this->assertSame('inner content', $outer->getOriginalContent());
    }

    public function testStatusAndContentHelperMethods() {
        $response = new CHTTP_Response('body text', 404);

        $this->assertSame(404, $response->status());
        $this->assertSame('body text', $response->content());
    }

    public function testHeaderSetsHeaderAndReturnsSelf() {
        $response = new CHTTP_Response();
        $result = $response->header('X-Custom', 'value1');

        $this->assertSame($response, $result);
        $this->assertSame('value1', $response->headers->get('X-Custom'));
    }

    public function testHeaderReplaceFlag() {
        $response = new CHTTP_Response();
        $response->header('X-Multi', 'first');
        $response->header('X-Multi', 'second', false);

        $this->assertSame(['first', 'second'], $response->headers->all('X-Multi'));
    }

    public function testWithHeadersFromArray() {
        $response = new CHTTP_Response();
        $result = $response->withHeaders([
            'X-One' => 'one',
            'X-Two' => 'two',
        ]);

        $this->assertSame($response, $result);
        $this->assertSame('one', $response->headers->get('X-One'));
        $this->assertSame('two', $response->headers->get('X-Two'));
    }

    public function testWithHeadersFromHeaderBag() {
        $bag = new Symfony\Component\HttpFoundation\HeaderBag(['X-Bag' => 'bag-value']);

        $response = new CHTTP_Response();
        $response->withHeaders($bag);

        $this->assertSame('bag-value', $response->headers->get('X-Bag'));
    }

    public function testWithCookieAddsCookieToHeaders() {
        $cookie = new Symfony\Component\HttpFoundation\Cookie('name', 'value');

        $response = new CHTTP_Response();
        $result = $response->withCookie($cookie);

        $this->assertSame($response, $result);
        $this->assertTrue($response->headers->getCookies() !== []);
        $this->assertSame('name', $response->headers->getCookies()[0]->getName());
        $this->assertSame('value', $response->headers->getCookies()[0]->getValue());
    }

    public function testCookieIsAliasForWithCookie() {
        $cookie = new Symfony\Component\HttpFoundation\Cookie('alias-name', 'alias-value');

        $response = new CHTTP_Response();
        $response->cookie($cookie);

        $this->assertSame('alias-name', $response->headers->getCookies()[0]->getName());
    }

    public function testGetCallbackReturnsNullByDefault() {
        $response = new CHTTP_Response();

        $this->assertNull($response->getCallback());
    }

    public function testWithExceptionStoresException() {
        $response = new CHTTP_Response();
        $exception = new RuntimeException('boom');

        $result = $response->withException($exception);

        $this->assertSame($response, $result);
        $this->assertSame($exception, $response->exception);
    }

    public function testThrowResponseThrowsResponseException() {
        $response = new CHTTP_Response('content', 500);

        $this->expectException(CHTTP_Exception_ResponseException::class);

        $response->throwResponse();
    }
}

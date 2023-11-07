<?php

namespace GuzzleHttp\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ClientTest extends TestCase
{

    public function testCanSendSynchronously()
    {
        $client = new Client(['handler' => new MockHandler([new Response()])]);
        $request = new Request('GET', 'http://example.com');
        $r = $client->send($request);
        self::assertInstanceOf(ResponseInterface::class, $r);
        self::assertSame(200, $r->getStatusCode());
    }
}

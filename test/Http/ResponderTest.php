<?php

namespace Novuso\Test\Common\Bundle\Http;

use Novuso\Test\Common\Bundle\Doubles\Http\IndexAction;
use Novuso\Test\Common\Bundle\Doubles\Http\IndexResponder;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use org\bovigo\vfs\vfsStream;

/**
 * @covers Novuso\Common\Bundle\Http\Responder
 */
class ResponderTest extends PHPUnit_Framework_TestCase
{
    public function test_that_it_is_callable()
    {
        $request = Request::create('https://example.com');
        $request->attributes->set('_format', 'html');
        $action = new IndexAction();
        $view = $action($request);
        $responder = new IndexResponder();
        $response = $responder($view);
        $this->assertTrue($response->isSuccessful());
    }

    public function test_that_json_response_returns_expected_instance()
    {
        $request = Request::create('https://example.com');
        $request->attributes->set('_format', 'json');
        $request->attributes->set('data', ['foo' => 'bar']);
        $action = new IndexAction();
        $view = $action($request);
        $responder = new IndexResponder();
        $response = $responder($view);
        $this->assertSame('{"foo":"bar"}', $response->getContent());
    }

    public function test_that_file_response_returns_expected_instance()
    {
        $this->createFilesystem();
        $request = Request::create(
            'https://example.com',
            'POST',
            [],
            [],
            [
                'upload' => [
                    'name'     => 'test.txt',
                    'type'     => 'text/plain',
                    'size'     => 26,
                    'tmp_name' => vfsStream::url('tmp/upload/test.txt'),
                    'error'    => UPLOAD_ERR_OK
                ]
            ]
        );
        $request->attributes->set('_format', 'txt');
        $action = new IndexAction();
        $view = $action($request);
        $responder = new IndexResponder();
        $response = $responder($view);
        $this->assertTrue($response->isSuccessful());
    }

    public function test_that_redirect_response_returns_expected_instance()
    {
        $request = Request::create('https://example.com');
        $request->attributes->set('_format', 'xml');
        $action = new IndexAction();
        $view = $action($request);
        $responder = new IndexResponder();
        $response = $responder($view);
        $this->assertSame(302, $response->getStatusCode());
    }

    public function test_that_stream_response_returns_expected_instance()
    {
        $request = Request::create('https://example.com');
        $request->attributes->set('_format', 'foo');
        $action = new IndexAction();
        $view = $action($request);
        $responder = new IndexResponder();
        $response = $responder($view);
        $this->assertTrue($response->isSuccessful());
    }

    protected function createFilesystem()
    {
        vfsStream::umask(0000);

        $filesystem = vfsStream::setup('tmp', null, [
            'upload' => [
                'test.txt' => 'Lorem ipsum dolor sit amet'
            ]
        ]);

        return $filesystem;
    }
}

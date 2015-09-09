<?php

namespace Novuso\Test\Common\Bundle\Http;

use Novuso\Test\Common\Bundle\Doubles\Http\IndexAction;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use org\bovigo\vfs\vfsStream;

/**
 * @covers Novuso\Common\Bundle\Http\Action
 */
class ActionTest extends PHPUnit_Framework_TestCase
{
    public function test_that_it_is_callable()
    {
        $request = Request::create('https://example.com');
        $action = new IndexAction();
        $view = $action($request);
        $this->assertSame($request, $view->request());
    }

    public function test_that_it_can_access_post_values()
    {
        $request = Request::create(
            'https://example.com',
            'POST',
            ['foo' => 'bar']
        );
        $action = new IndexAction();
        $view = $action($request);
        $parameters = $view->parameters();
        $this->assertSame('bar', $parameters['foo']);
    }

    public function test_that_it_can_access_query_values()
    {
        $request = Request::create(
            'https://example.com',
            'GET',
            ['foo' => 'bar']
        );
        $action = new IndexAction();
        $view = $action($request);
        $parameters = $view->parameters();
        $this->assertSame(['foo' => 'bar'], $parameters['get']);
    }

    public function test_that_it_can_access_attribute_values()
    {
        $request = Request::create('https://example.com');
        $request->attributes->set('foo', 'bar');
        $action = new IndexAction();
        $view = $action($request);
        $parameters = $view->parameters();
        $this->assertSame(['foo' => 'bar'], $parameters['attr']);
    }

    public function test_that_it_can_access_server_values()
    {
        $request = Request::create('https://example.com');
        $action = new IndexAction();
        $view = $action($request);
        $parameters = $view->parameters();
        $this->assertSame('127.0.0.1', $parameters['ip']);
    }

    public function test_that_it_can_access_header_values()
    {
        $request = Request::create('https://example.com');
        $action = new IndexAction();
        $view = $action($request);
        $parameters = $view->parameters();
        $this->assertSame('example.com', $parameters['host']);
    }

    public function test_that_it_can_access_file_values()
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
        $action = new IndexAction();
        $view = $action($request);
        $parameters = $view->parameters();
        $this->assertSame('Lorem ipsum dolor sit amet', file_get_contents($parameters['upload']));
    }

    public function test_that_it_can_access_cookie_values()
    {
        $request = Request::create(
            'https://example.com',
            'GET',
            [],
            ['foo' => 'bar']
        );
        $action = new IndexAction();
        $view = $action($request);
        $parameters = $view->parameters();
        $this->assertSame(['foo' => 'bar'], $parameters['cookies']);
    }

    public function test_that_it_can_access_session_values()
    {
        $request = Request::create('https://example.com');
        $session = new Session(new MockArraySessionStorage());
        $session->set('foo', 'bar');
        $request->setSession($session);
        $action = new IndexAction();
        $view = $action($request);
        $parameters = $view->parameters();
        $this->assertSame(['foo' => 'bar'], $parameters['session']);
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

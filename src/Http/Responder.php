<?php

namespace Novuso\Common\Bundle\Http;

use Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Responder is the base class for an HTTP response formatter
 *
 * @copyright Copyright (c) 2015, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
abstract class Responder
{
    /**
     * View
     *
     * @var View
     */
    protected $view;

    /**
     * Creates a response
     *
     * @return Response
     *
     * @throws Exception When unable to create a response
     */
    abstract protected function call();

    /**
     * Invokes the responder
     *
     * @param View $view The view
     *
     * @return Response
     *
     * @throws Exception When unable to create a response
     */
    public function __invoke(View $view)
    {
        $this->view = $view;

        return $this->call();
    }

    /**
     * Formats a template path
     *
     * @param string $name      The template name
     * @param string $format    The template format
     * @param string $extension The template extension
     *
     * @return string
     */
    protected function template($name, $format = 'html', $extension = 'twig')
    {
        return sprintf('%s.%s.%s', str_replace(':', '/', $name), $format, $extension);
    }

    /**
     * Retrieves merged view parameters
     *
     * @param string $key The view model key
     *
     * @return array
     */
    protected function parameters($key = 'data')
    {
        $parameters = $this->view->parameters();
        $parameters[$key] = $this->view->data();

        return $parameters;
    }

    /**
     * Creates a standard response
     *
     * @param mixed  $content The response content
     * @param int    $status  The status code
     * @param array  $headers An array of response headers
     *
     * @return Response
     */
    protected function response($content, $status = Response::HTTP_OK, array $headers = [])
    {
        return new Response($content, $status, $headers);
    }

    /**
     * Creates a JSON response
     *
     * @param mixed  $data    The response data
     * @param int    $status  The status code
     * @param array  $headers An array of response headers
     *
     * @return JsonResponse
     */
    protected function jsonResponse($data, $status = Response::HTTP_OK, array $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Creates a file response
     *
     * Options and defaults:
     *
     *     $options = [
     *         'public'              => true,
     *         'content_disposition' => null,
     *         'auto_etag'           => false,
     *         'auto_last_modified'  => true
     *     ];
     *
     * @param SplFileInfo|string $file    The file to stream
     * @param int                $status  The status code
     * @param array              $headers An array of response headers
     * @param array              $options Additional options
     *
     * @return BinaryFileResponse
     */
    protected function fileResponse($file, $status = Response::HTTP_OK, array $headers = [], array $options = [])
    {
        $options = array_merge([
            'public'              => true,
            'content_disposition' => null,
            'auto_etag'           => false,
            'auto_last_modified'  => true
        ], $options);

        return new BinaryFileResponse(
            $file,
            $status,
            $headers,
            $options['public'],
            $options['content_disposition'],
            $options['auto_etag'],
            $options['auto_last_modified']
        );
    }

    /**
     * Creates a redirect response
     *
     * @param string $url     The URL to redirect to
     * @param int    $status  The status code
     * @param array  $headers An array of response headers
     *
     * @return RedirectResponse
     */
    protected function redirectResponse($url, $status = Response::HTTP_FOUND, array $headers = [])
    {
        return new RedirectResponse($url, $status, $headers);
    }

    /**
     * Creates a streamed response
     *
     * @param callable $callback A callback function to echo content
     * @param int      $status   The status code
     * @param array    $headers  An array of response headers
     *
     * @return StreamedResponse
     */
    protected function streamResponse(callable $callback, $status = Response::HTTP_OK, array $headers = [])
    {
        return new StreamedResponse($callback, $status, $headers);
    }
}

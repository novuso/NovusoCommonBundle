<?php

namespace Novuso\Common\Bundle\Middleware;

use Exception;
use Negotiation\FormatNegotiator;
use Negotiation\FormatNegotiatorInterface;
use Negotiation\LanguageNegotiator;
use Negotiation\NegotiatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\Serializer\Encoder\ChainDecoder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

/**
 * ContentNegotiation provides content negotiation as HttpKernel middleware
 *
 * @copyright Copyright (c) 2015, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
class ContentNegotiation implements HttpKernelInterface, TerminableInterface
{
    /**
     * Decorated kernel
     *
     * @var HttpKernelInterface
     */
    protected $kernel;

    /**
     * Format priorities
     *
     * @var string[]
     */
    protected $formatPriorities;

    /**
     * Language priorities
     *
     * @var string[]
     */
    protected $languagePriorities;

    /**
     * Format negotiator
     *
     * @var FormatNegotiatorInterface
     */
    protected $formatNegotiator;

    /**
     * Language negotiator
     *
     * @var NegotiatorInterface
     */
    protected $languageNegotiator;

    /**
     * Content decoder
     *
     * @var DecoderInterface
     */
    protected $contentDecoder;

    /*
     * The following methods are derived from code of Stack Negotiation
     * (1.0.0 - 2014-10-28)
     *
     * Copyright (c) William Durand <william.durand1@gmail.com>
     *
     * Permission is hereby granted, free of charge, to any person obtaining a
     * copy of this software and associated documentation files
     * (the "Software"), to deal in the Software without restriction, including
     * without limitation the rights to use, copy, modify, merge, publish,
     * distribute, sublicense, and/or sell copies of the Software, and to
     * permit persons to whom the Software is furnished to do so, subject to
     * the following conditions:
     *
     * The above copyright notice and this permission notice shall be included
     * in all copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
     * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
     * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
     * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
     * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
     * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
     * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
     */

    /**
     * Constructs ContentNegotiation
     *
     * @param HttpKernelInterface            $kernel             The kernel
     * @param string[]                       $formatPriorities   The format priorities
     * @param string[]                       $languagePriorities The language priorities
     * @param FormatNegotiatorInterface|null $formatNegotiator   The format negotiator
     * @param NegotiatorInterface|null       $languageNegotiator The language negotiator
     * @param DecoderInterface|null          $contentDecoder     The content decoder
     */
    public function __construct(
        HttpKernelInterface $kernel,
        array $formatPriorities = [],
        array $languagePriorities = [],
        FormatNegotiatorInterface $formatNegotiator = null,
        NegotiatorInterface $languageNegotiator = null,
        DecoderInterface $contentDecoder = null
    ) {
        $this->kernel = $kernel;
        $this->formatPriorities = $formatPriorities;
        $this->languagePriorities = $languagePriorities;
        $this->formatNegotiator = $formatNegotiator ?: new FormatNegotiator();
        $this->languageNegotiator = $languageNegotiator ?: new LanguageNegotiator();
        $this->contentDecoder = $contentDecoder ?: new ChainDecoder([new JsonEncoder(), new XmlEncoder()]);
    }

    /**
     * Handles a request to convert it to a response
     *
     * @param Request $request The request
     * @param int     $type    The type of request
     * @param bool    $catch   Whether to catch exceptions or not
     *
     * @return Response
     *
     * @throws Exception When an exception occurs during processing
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $accept = $request->headers->get('Accept');

        if ($accept !== null) {
            $priorities = $this->formatNegotiator->normalizePriorities($this->formatPriorities);
            $accept = $this->formatNegotiator->getBest($accept, $priorities);

            $request->attributes->set('_accept', $accept);

            if ($accept !== null & !$accept->isMediaRange()) {
                $mimeType = $accept->getValue();
                $request->attributes->set('_mime_type', $mimeType);
                $request->attributes->set('_format', $this->formatNegotiator->getFormat($mimeType));
            }
        }

        $acceptLang = $request->headers->get('Accept-Language');

        if ($acceptLang !== null) {
            $acceptLang = $this->languageNegotiator->getBest($acceptLang, $this->languagePriorities);

            $request->attributes->set('_accept_language', $acceptLang);

            if ($acceptLang !== null) {
                $request->attributes->set('_language', $acceptLang->getValue());
            }
        }

        try {
            $this->decodeBody($request);
        } catch (BadRequestHttpException $exception) {
            if ($catch === true) {
                return new Response($exception->getMessage(), Response::HTTP_BAD_REQUEST);
            }
            throw $exception;
        }

        return $this->kernel->handle($request, $type, $catch);
    }

    /**
     * Terminates the request/response cycle
     *
     * @param Request  $request  The request
     * @param Response $response The response
     *
     * @return void
     */
    public function terminate(Request $request, Response $response)
    {
        if ($this->kernel instanceof TerminableInterface) {
            $this->kernel->terminate($request, $response);
        }
    }

    /**
     * Decodes the request body
     *
     * @param Request $request The request
     *
     * @return void
     */
    protected function decodeBody(Request $request)
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $contentType = $request->headers->get('Content-Type', '');
            $format = $this->formatNegotiator->getFormat($contentType);

            if (!$this->contentDecoder->supportsDecoding($format)) {
                return;
            }

            $content = $request->getContent();

            if (!empty($content)) {
                try {
                    $data = $this->contentDecoder->decode($content, $format);
                } catch (Exception $exception) {
                    $data = null;
                }

                if (is_array($data)) {
                    $request->request->replace($data);
                } else {
                    $message = sprintf('Invalid %s message received', $format);
                    throw new BadRequestHttpException($message);
                }
            }
        }
    }
}

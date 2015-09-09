<?php

namespace Novuso\Test\Common\Bundle\Doubles\Http;

use Novuso\Common\Bundle\Http\Responder;
use Symfony\Component\HttpFoundation\Response;

class IndexResponder extends Responder
{
    const TEMPLATE = 'common:default:index';

    protected function call()
    {
        $template = $this->template(static::TEMPLATE);
        $format = $this->view->format();
        switch ($format) {
            case 'html':
                return $this->response('<html></html>');
                break;
            case 'json':
                $parameters = $this->parameters();

                return $this->jsonResponse($parameters['attr']['data']);
                break;
            case 'txt':
                $parameters = $this->parameters();

                return $this->fileResponse($parameters['upload']);
                break;
            case 'xml':
                return $this->redirectResponse('/redirect');
            default:
                return $this->streamResponse(function () {
                    echo '<html></html>';
                });
                break;
        }
    }
}

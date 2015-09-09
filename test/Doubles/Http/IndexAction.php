<?php

namespace Novuso\Test\Common\Bundle\Doubles\Http;

use Novuso\Common\Bundle\Http\Action;

class IndexAction extends Action
{
    protected function call()
    {
        $parameters = [];
        $parameters['foo'] = $this->post('foo');
        $parameters['post'] = $this->post();
        $parameters['get'] = $this->query();
        $parameters['attr'] = $this->attributes();
        $parameters['ip'] = $this->server('REMOTE_ADDR');
        $parameters['host'] = $this->headers('host');
        $parameters['upload'] = $this->files('upload');
        $parameters['cookies'] = $this->cookies();
        $parameters['session'] = $this->session();
        $parameters['sessfoo'] = $this->session('foo');

        return $this->view(null, $parameters);
    }
}

<?php

namespace Sanpi\Behatch\HttpCall\Request;

use Behat\Mink\Mink;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BrowserKit extends Goutte
{
    /**
     * headers are no more stored on client, because client does not flush them when reset/restart session.
     * They are on Behat\Mink\Driver\BrowserKitDriver and there is no way to get them.
     *
     * @var array
     */
    private $requestHeaders = [];

    public function send($method, $url, $parameters = [], $files = [], $content = null, $headers = [])
    {
        foreach ($files as $originalName => &$file) {
            $file = new UploadedFile($file, $originalName);
        }
        $page = parent::send($method, $url, $parameters, $files, $content, $this->requestHeaders);
        $this->resetHttpHeaders();

        return $page;
    }

    public function getServer()
    {
        return $this->getRequest()
            ->server->all();
    }

    public function getParameters()
    {
        return $this->getRequest()
            ->query->all();
    }

    public function setHttpHeader($name, $value)
    {
        $this->requestHeaders["HTTP_$name"] = $value;
    }

    private function resetHttpHeaders()
    {
        $this->requestHeaders = [];
    }
}

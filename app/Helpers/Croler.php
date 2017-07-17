<?php

namespace App\Helpers;
use DOMDocument;

class Croler
{

    private $options = [
        'http' => [
            'method'  => 'GET',
            'headers' => [
                'User-Agent: Test/0.1',
            ]
        ]
    ];

    private $context;
    private $document;

    public function __construct(array $options = null)
    {
        if($options){
            $this->options = $options;
        }
        $this->context = stream_context_create($this->options);
        $this->document = new DOMDocument();
    }

    public function getPageHtml($url)
    {
        @$this->document->loadHTML(@file_get_contents($url, false, $this->context));

        return $this->document;
    }

    public function getOptions()
    {
        return $this->options;
    }





}
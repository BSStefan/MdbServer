<?php

namespace App\Helpers;
use DOMDocument;

class Crawler
{
    //Default settings
    private $options = [
        'http' => [
            'method'  => 'GET',
            'headers' => [
                'User-Agent: Test/0.1',
                'Cookie : SID=di8873lee23797g0ttgcd13t93; lbconpers=rd5o00000000000000000000ffffc2e8220eo80; _ga=GA1.2.511727334.1499778335; _gid=GA1.2.969828138.1500542372'
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
<?php

namespace App\Repositories\Admin;

use App\Helpers\Croler;

class CrolerRepository
{
    protected $croler;

    public function __construct(Croler $croler)
    {
        $this->croler = $croler;
    }

    public function findLinks($url)
    {
        $doc = $this->croler->getPageHtml($url);

        $arrayLinks = [];

        $h2Nodes = $doc->getElementsByTagName('h2');

        foreach($h2Nodes as $h2){
            if($h2->firstChild instanceof \DOMElement){
                if($h2->firstChild->tagName == 'a'){
                    array_push($arrayLinks, 'http:' . $h2->firstChild->getAttribute('href'));
                }
            }
        }

        return $arrayLinks;
    }

    public function findTitles($url)
    {
        $titles = [];

        $urls = $this->findLinks($url);

        foreach($urls as $oneUrl)
        {
            $doc = $this->croler->getPageHtml($oneUrl);
            $tables = $doc->getElementsByTagName('table');
            foreach($tables as $table){
                $tr = $table->firstChild;
                foreach($tr->childNodes as $td){
                    if($td instanceof \DOMElement && $td->nodeValue != 'Originalni naslov:'){
                        array_push($titles,$td->nodeValue);
                    }
                }
            }
        }

        return $titles;
    }

}
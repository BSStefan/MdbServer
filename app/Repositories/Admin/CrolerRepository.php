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

    public function findTimes($url)
    {
        $doc = $this->croler->getPageHtml($url);
        $movies = [];
        $divs = $doc->getElementsByTagName('div');

        foreach($divs as $div) {
            $classes = $div->getAttribute('class');
            if(strpos($classes, 'info') !== false) {
                $hTag = $div->getElementsByTagName('h2');
                $movie['title'] = $hTag[0]->nodeValue;
            }
            if(strpos($classes, 'start-times') !== false) {
                $movie = [
                    'a'    => [],
                    'time' => [],
                    'room' => []
                ];
                $aTags = $div->getElementsByTagName('a');
                $pTags = $div->getElementsByTagName('p');
                foreach($aTags as $a){
                    array_push($movie['a'], trim($a->getAttribute('href')));
                }
                foreach($pTags as $p) {
                    $class = $p->getAttribute('class');
                    if(strpos($class, 'time-desc') !== false) {
                        array_push($movie['time'], trim($p->nodeValue));
                    }
                    else if(strpos($class, 'room-desc') !== false) {
                        array_push($movie['room'], trim($p->nodeValue));
                    }
                }
                array_push($movies, $movie);
            }
        }
        return $movies;
    }


}
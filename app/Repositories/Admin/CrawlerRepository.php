<?php

namespace App\Repositories\Admin;

use App\Helpers\Crawler;

class CrawlerRepository
{
    /**
     * @var Crawler
     */
    protected $crawler;

    /**
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Find links for movie separate page
     * Return array of links
     *
     * @param string $url
     * @return array
     */
    public function findLinks($url)
    {
        $doc = $this->crawler->getPageHtml($url);

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

    /**
     * Find all titles current in cinema
     * Return array of titles
     *
     * @param string $url
     * @return array
     */
    public function findTitles($url)
    {
        $titles = [];

        $urls = $this->findLinks($url);

        foreach($urls as $oneUrl){
            $doc    = $this->crawler->getPageHtml($oneUrl);
            $tables = $doc->getElementsByTagName('table');
            foreach($tables as $table){
                $tr = $table->firstChild;
                foreach($tr->childNodes as $td){
                    if($td instanceof \DOMElement && $td->nodeValue != 'Originalni naslov:'){
                        array_push($titles, $td->nodeValue);
                    }
                }
            }
        }

        return $titles;
    }

    /**
     * Find all times for all movies for one day
     * Return formatted array of all movies for one day
     *
     * @param string $url
     * @return array
     */
    public function findTimes($url)
    {
        $doc     = $this->crawler->getPageHtml($url);
        $movies  = [];
        $selects = $doc->getElementsByTagName('select');
        $divs    = $doc->getElementsByTagName('div');

        foreach($selects as $select){
            $name = $select->getAttribute('name');
            if($name == 'centerId'){
                $options = $select->getElementsByTagName('option');
                foreach($options as $option){
                    if($option->hasAttribute('selected')){
                        $cinema = $option->nodeValue;
                    }
                }
            }
            if($name == 'date'){
                $options = $select->getElementsByTagName('option');
                foreach($options as $option){
                    if($option->hasAttribute('selected')){
                        $date = $option->getAttribute('value');
                    }
                }
            }
        }

        foreach($divs as $div){
            $classes = $div->getAttribute('class');
            if(strpos($classes, 'overview-element') !== false){
                $movieTitle = '';
                $childDivs  = $div->getElementsByTagName('div');
                foreach($childDivs as $childDiv){
                    $childClasses = $childDiv->getAttribute('class');
                    if(strpos($childClasses, 'starBoxSmall') !== false){
                        $pTags      = $div->getElementsByTagName('p');
                        $movieTitle = $pTags[1]->nodeValue;
                    }
                    if(strpos($childClasses, 'start-times') !== false){
                        $aTags = $childDiv->getElementsByTagName('a');
                        foreach($aTags as $a){
                            $movieTime        = [
                                'title'  => utf8_decode($movieTitle),
                                'cinema' => $cinema,
                                'date'   => $date
                            ];
                            $pTags            = $a->getElementsByTagName('p');
                            $movieTime['url'] = trim($a->getAttribute('href'));
                            foreach($pTags as $p){
                                $class = $p->getAttribute('class');
                                if(strpos($class, 'time-desc') !== false){
                                    $movieTime['time'] = trim($p->nodeValue);
                                }
                                else if(strpos($class, 'room-desc') !== false){
                                    $movieTime['room'] = trim($p->nodeValue);
                                }
                            }
                            array_push($movies, $movieTime);
                        }

                    }
                }
            }
        }

        return $movies;
    }

}
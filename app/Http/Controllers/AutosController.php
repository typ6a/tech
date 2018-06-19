<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class AutosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }
    public function getAutos()
    {
        ini_set('max_execution_time', 180);
        $this->getRstAds();
        $this->getAutoriaAds();
    }

    public function getHtml($url)
    {
        $htmlFilePath = '../storage/autos/' . md5($url) . '.html';
            if (!file_exists($htmlFilePath)){
                $html = file_get_contents($url);
                $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
                file_put_contents($htmlFilePath, $html);
                usleep(rand(5000000, 10000000));
            }
            else {
                $html = file_get_contents($htmlFilePath);
                $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
            }
        // $html = file_get_contents($url);
        return $html;
    }

    public function getAutoriaAds()
    {
        $cars = [];
        $mainUrl ='';
        $url = 'https://auto.ria.com/search/?categories.main.id=1&price.currency=1&sort[0].order=dates.created.desc&abroad.not=0&custom.not=1&page=0&size=100';
        $html = $this->getHtml($url);
        $this->crawler = new Crawler($html);
        $ads = $this->crawler->filter('#searchResults .ticket-item.new__ticket.t');
        $ads->each(function (Crawler $auto) use (&$cars, $mainUrl){
            $title            = trim($auto->filter('.content-bar .content .head-ticket .item.ticket-title')->text());
            $url              = trim($auto->filter('.content-bar a')->attr('href'));
            $pictureUrl       = trim($auto->filter('img')->attr('src'));
            $shortDescription = preg_replace('| +|', ' ', trim($auto->filter('p.descriptions-ticket span')->text()));
            $priceUsd         = trim($auto->filter('.price-ticket')->attr('data-main-price'));
            $priceUah         = str_replace(' ', '', trim($auto->filter('span[data-currency="UAH"]')->text()));
            $mileage          = trim(str_replace(' тыс. км', '000', $auto->filter('.item-char')->eq(0)->text()));
            $location         = trim($auto->filter('.item-char')->eq(1)->text());
            $engine           = trim($auto->filter('.item-char')->eq(2)->text());
            $transmission     = trim($auto->filter('.item-char')->eq(3)->text());
            $updated          = trim($auto->filter('.footer_ticket>span')->attr('data-add-date'));
            $added            = '';
            $year             = substr($title, -4);
            $brand            = stristr($title , ' ', true);
            $model            = trim(str_replace($year, '', str_replace($brand, '', $title)));
            $premiumAdd       = true;
            $cars[] = [
                'title'            => $title,
                'url'              => $url,
                'year'             => $year,
                'brand'            => $brand,
                'model'            => $model,
                'pictureUrl'       => $pictureUrl,
                'shortDescription' => $shortDescription,
                'priceUsd'         => $priceUsd,
                'priceUah'         => $priceUah,
                'mileage'          => $mileage,
                'location'         => $location,
                'engine'           => $engine,
                'transmission'     => $transmission,
                'added'            => $added,
                'updated'          => $updated,
                'premiumAdd'       => $premiumAdd,
            ];
        });
        pre($cars);
    }

    public function getRstAds()
    {
        $cars = [];
        $mainUrl ='http://rst.ua';
        for ($page = 1; $page <= 10; $page++) { 
            # code...
            $url = 'http://rst.ua/oldcars/?task=newresults&make%5B%5D=0&year%5B%5D=0&year%5B%5D=0&price%5B%5D=0&price%5B%5D=0&engine%5B%5D=0&engine%5B%5D=0&gear=0&fuel=0&drive=0&condition=0&from=sform&start=' . $page;
            $html = $this->getHtml($url);
            $this->crawler = new Crawler($html);
            $ads = $this->crawler->filter('.rst-page-wrap .rst-ocb-i');
            $ads->each(function (Crawler $auto) use (&$cars, $mainUrl){
                if (count($auto->filter('a h3 span'))) {
                    $title            = str_replace('продам ', '', trim($auto->filter('a h3 span')->text()));
                    $url              = $mainUrl . trim($auto->filter('a')->attr('href'));
                    $pictureUrl       = trim($auto->filter('a img')->attr('src'));
                    $shortDescription = preg_replace('| +|', ' ', trim($auto->filter('div.rst-ocb-i-d-d')->text()));
                    $priceUsd         = preg_replace('/[^0-9]/', '', trim($auto->filter('li.rst-ocb-i-d-l-i span.rst-uix-grey')->text()));
                    $priceUah         = preg_replace('/[^0-9]/', '', trim($auto->filter('li.rst-ocb-i-d-l-i span.rst-ocb-i-d-l-i-s.rst-ocb-i-d-l-i-s-p')->text()));
                    $mileage          = substr(preg_replace('/[^0-9]/', '', trim($auto->filter('li.rst-ocb-i-d-l-i')->eq(1)->text())), 4);
                    $location         = str_replace('Область: ', '', trim($auto->filter('li.rst-ocb-i-d-l-j')->eq(0)->text()));
                    $engine           = stristr(str_replace('Двиг.: ', '', trim($auto->filter('li.rst-ocb-i-d-l-i')->eq(2)->text())), '(', true);
                    $transmission     = str_replace([' ', '(', ')'], '', stristr(str_replace('Двиг.: ', '', trim($auto->filter('li.rst-ocb-i-d-l-i')->eq(2)->text())), '('));
                    $added            = str_replace('размещено ', '', stristr(trim($auto->filter('div.rst-ocb-i-s')->text()), 'размещено'));
                    $updated          = str_replace('обновлено ', '', stristr(trim($auto->filter('div.rst-ocb-i-s')->text()), 'обновлено'));
                    $year             = trim($auto->filter('span.rst-ocb-i-d-l-i-s')->eq(2)->text());
                    $brand            = stristr($title , ' ', true);
                    $model            = trim(str_replace($brand, '', $title));
                    $premiumAdd       = true;
                    $cars[] = [
                        'title'            => $title,
                        'url'              => $url,
                        'year'             => $year,
                        'brand'            => $brand,
                        'model'            => $model,
                        'pictureUrl'       => $pictureUrl,
                        'shortDescription' => $shortDescription,
                        'priceUsd'         => $priceUsd,
                        'priceUah'         => $priceUah,
                        'mileage'          => $mileage,
                        'location'         => $location,
                        'engine'           => $engine,
                        'transmission'     => $transmission,
                        'added'            => $added,
                        'updated'          => $updated,
                        'premiumAdd'       => $premiumAdd,
                    ];
                }
            });
        }
        pre($cars,1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

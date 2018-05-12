<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use GuzzleHttp\Client;

class IndexController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(){

        $client = new Client(['base_uri' => 'localhost:8001/']);
        $response = $client->request('GET', 'hotels');
        $hotels = json_decode($response->getBody()->getContents());


        $client = new Client(['base_uri' => 'localhost:8001/']);
        $response = $client->request('GET', 'rooms');
        $rooms = json_decode($response->getBody()->getContents());

        return $this->render(':Otel:index.html.twig',array('hotels' => $hotels,'rooms'=>$rooms));
    }
}

<?php

namespace AppBundle\Controller;

use Doctrine\ORM\Persisters\Entity\JoinedSubclassPersister;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HotelsController extends Controller
{
    /**
     * @Route("/hotels",name="hotels")
     */
    public function IndexAction(){
        return $this->render(':Otel:hotels.html.twig');
    }

    /**
     * @Route("/get-hotels")
     */
    public function getHotelAction(){
        $client = new Client(['base_uri'=>'localhost:8001/']);
        $response = $client->request('GET','hotels');

        return new JsonResponse($response->getBody()->getContents());
    }

    /**
     * @Route("/hotel-details", name="hotel-details")
     */
    public function HotelDetailsAction(){
        return $this->render(':Otel:hotel-details.html.twig');
    }
}

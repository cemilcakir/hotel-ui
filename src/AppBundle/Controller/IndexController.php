<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\Tests\Compiler\J;
use Symfony\Component\HttpFoundation\JsonResponse;

class IndexController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(){
        $session = $this->get('session');

        return $this->render(':Otel:index.html.twig');
    }

    /**
     * @Route("/hotelsRooms")
     */
    public function hotelsRoomsAction(){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);

        $response = $client->request('GET', 'hotels');
        $hotels = json_decode($response->getBody()->getContents());

        $response = $client->request('GET', 'rooms');
        $rooms = json_decode($response->getBody()->getContents());

        $res = new JsonResponse();
        $res->setContent(json_encode(array("hotels"=>$hotels,"rooms"=>$rooms)));

        return new JsonResponse($res->getContent());
    }
}

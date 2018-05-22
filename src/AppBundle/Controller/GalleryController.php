<?php

namespace AppBundle\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class GalleryController extends Controller
{
    /**
     * @Route("/gallery",name="gallery")
     */
    public function IndexAction(){
        return $this->render(':Otel:gallery.html.twig');
    }
    /**
     * @Route("/images")
     */
    public function imagesAction(){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('get','images');

        return new JsonResponse($response->getBody()->getContents());
    }
}

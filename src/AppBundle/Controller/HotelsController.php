<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HotelsController extends Controller
{
    /**
     * @Route("/hotels",name="hotels")
     */
    public function IndexAction(){
        return $this->render(':Otel:hotels.html.twig');
    }

    /**
     * @Route("/hotel-details", name="hotel-details")
     */
    public function HotelDetailsAction(){
        return $this->render(':Otel:hotel-details.html.twig');
    }
}

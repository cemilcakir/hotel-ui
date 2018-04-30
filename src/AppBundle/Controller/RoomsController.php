<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class RoomsController extends Controller
{
    /**
     * @Route("/room-details", name="room-details")
     */
    public function RoomDetailsAction(){
        return $this->render(':Otel:room-details.html.twig');
    }
}

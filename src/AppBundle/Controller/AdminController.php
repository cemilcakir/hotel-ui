<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AdminController extends Controller
{
    /**
     * @Route("/admin",name="admin")
     */
    public function hotelIndexAction(){
        return $this->render(':Otel:admin-index.html.twig');
    }

    /**
     * @Route("/edit-hotel",name="edit-hotel")
     */
    public function editHotelAction(){
        return $this->render(':Otel:admin-edit-hotel.html.twig');
    }

    /**
     * @Route("/room-index",name="room-index")
     */
    public function roomIndexAction(){
        return $this->render(':Otel:admin-room-index.html.twig');
    }

    /**
     * @Route("/edit-room",name="edit-room")
     */
    public function editRoomAction(){
        return $this->render(':Otel:admin-edit-room.html.twig');
    }

    /**
     * @Route("/edit-hotel-pics",name="edit-hotel-pics")
     */
    public function editHotelPicsAction(){
        return $this->render(':Otel:admin-hotel-pic.html.twig');
    }

    /**
     * @Route("/edit-room-pics",name="edit-room-pics")
     */
    public function editRoomPicsAction(){
        return $this->render(':Otel:admin-room-pic.html.twig');
    }
}

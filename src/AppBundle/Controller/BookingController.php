<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use GuzzleHttp\Client;

class BookingController extends Controller
{
    /**
     * @Route("/get-user")
     */
    public function getUserAction(){
        $session = $this->get('session');
        $userFirstName = $session->get('userFirstName');
        $userLastName = $session->get('userLastName');
        $userId = $session->get('userId');

        $res = new JsonResponse();
        $res->setContent(json_encode(array("userFirstName"=>$userFirstName,"userLastName"=>$userLastName,"userId"=>$userId)));

        return $res;
    }

    /**
     * @Route("/rezerve-et")
     */
    public function rezerveEtAction(Request $request){
        $userId= $request->request->get('userId');
        $hotelId= $request->request->get('hotelId');
        $roomId= $request->request->get('roomId');
        $peopleCount= $request->request->get('peopleCount');
        $price= $request->request->get('price');
        $entDate= $request->request->get('entranceDate');
        $lDate= $request->request->get('leaveDate');

        $body = json_encode(array(
            "user_id"=>$userId,
            "hotel_id"=>$hotelId,
            "room_id"=>$roomId,
            "people_count"=>$peopleCount,
            "price"=>$price,
            "entrance_date"=>$entDate,
            "leave_date"=>$lDate
        ));

        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);

        $response = $client->request("POST","/bookings",['body'=>$body,
            'headers' => [
            'Content-Type' => 'application/json',
        ]]);

        return new JsonResponse($response->getBody()->getContents());
    }
}

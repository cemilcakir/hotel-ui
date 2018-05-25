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

    /**
     * @Route("/book-info",name="book-info")
     */
    public function bookInfoAction(){
        return $this->render(':Otel:book-info.html.twig');
    }

    /**
     * @Route("/get-book-info")
     */
    public function getBookInfoAction(){
        $userId = $this->get('session')->get('userId');
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);

        $response = $client->request('GET','users/'.$userId.'/booking');
        $userBookings = json_decode($response->getBody()->getContents());

        $responseText = array();

        foreach ($userBookings as $userBooking) {
            $hotelResponse = json_decode($client->request('GET','hotels/'.$userBooking->hotel_id)->getBody()->getContents());
            $roomResponse = json_decode($client->request('GET','rooms/'.$userBooking->room_id)->getBody()->getContents());

            $responseText = array_merge_recursive($responseText,array(
                "bookId"=>$userBooking->id,
                "hotelName"=>$hotelResponse->name,
                "roomType"=>$roomResponse->type,
                "star"=>$hotelResponse->star,
                "entDate"=>$userBooking->entrance_date,
                "leaveDate"=>$userBooking->leave_date,
                "peopleCount"=>$userBooking->people_count,
                "price"=>$userBooking->price,
                "phone"=>$hotelResponse->phone,
                "mail"=>$hotelResponse->mail,
                "address"=>$hotelResponse->address
            ));
        }

        return new JsonResponse(json_encode($responseText));
    }

    /**
     * @Route("/cancel-book")
     */
    public function cancelBookAction(Request $request){
        $bookId = $request->request->get('id');

        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);

        $response = $client->request('DELETE','bookings/'.$bookId);
        return new JsonResponse($response->getStatusCode());
    }
}

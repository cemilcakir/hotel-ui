<?php

namespace AppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use GuzzleHttp\Client;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminController extends Controller
{
    /**
     * @Route("/admin",name="admin")
     */
    public function hotelIndexAction(){

        $session = $this->get('session');
        if($session->get('userRole') != 'ROLE_ADMIN'){
            $response = $this->forward('AppBundle:Index:index');
            return $response;
        }
        else{
            $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
            $response = $client->request('GET','hotels');
            $hotels = json_decode($response->getBody()->getContents());

            return $this->render(':Otel:admin-index.html.twig',array('hotels' => $hotels));
        }
    }

    /**
     * @Route("/getHotelId")
     */
    public function getHotelIdAction(Request $request){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('GET','hotels/'.$request->request->get('id'));
        $hotel = json_decode($response->getBody()->getContents());

        return new JsonResponse($hotel);
    }

    /**
     * @Route("/edit-hotel",name="edit-hotel")
     */
    public function editHotelAction(Request $request){
        return $this->render(':Otel:admin-edit-hotel.html.twig');
    }

    /**
     * @Route("/save-edited-hotel")
     */
    public function saveEditedHotelAction(Request $request){
        $theHotel = json_encode(array(
            "name" => $request->request->get('name'),
            "star" =>$request->request->get('star'),
            "link" => $request->request->get('link'),
            "mail" => $request->request->get('mail'),
            "detail" => $request->request->get('detail'),
            "phone" => $request->request->get('phone')));
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('PATCH','hotels/'.$request->request->get('id'), [
            'body' => $theHotel,
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        return new JsonResponse($response->getBody()->getContents());

    }

    /**
     * @Route("/add-hotel-index",name="add-hotel-index")
     */
    public function addHotelIndexAction(){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('GET','il');
        $iller = json_decode($response->getBody()->getContents());

        return $this->render(':Otel:admin-add-hotel.html.twig',array("iller"=>$iller));
    }

    /**
     * @Route("/addhotel")
     */
    public function addHotelAction(Request $request){

        $hotel = array(
            'name' =>$request->request->get('name'),
            'star'=>$request->request->get('star'),
            'province'=>$request->request->get('province'),
            'county'=>$request->request->get('county'),
            'link'=>$request->request->get('link'),
            'mail'=>$request->request->get('mail'),
            'phone'=>$request->request->get('phone'),
            'detail'=>$request->request->get('detail'),
            'address'=>$request->request->get('address')
        );

        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('POST','hotels', [
            'body' => json_encode($hotel),
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        return new JsonResponse($response->getBody()->getContents());
    }

    /**
     * @Route("/deleteHotel")
     */
    public function deleteHotelAction(Request $request){

        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('delete','hotels/'.$request->request->get('id'));
        return new JsonResponse("silindi");
    }

    /**
     * @Route("/admin/ilce")
     */
    public function ilceAction(Request $request) {
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('GET','ilces/'.$request->get('il'));
        $ilceler = json_decode($response->getBody()->getContents());

        echo json_encode(array("ilceler"=>$ilceler));die();
    }

    /**
     * @Route("/getHotelPics")
     */
    public function getHotelPicsAction(Request $request){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('GET','/hotels/'.$request->get('id').'/images');

        return new JsonResponse($response->getBody()->getContents());
    }

    /**
     * @Route("/edit-hotel-pics",name="edit-hotel-pics")
     */
    public function editHotelPicsAction(){
        return $this->render(':Otel:admin-hotel-pic.html.twig');
    }

    /**
     * @Route("/add-hotel-pic")
     */
    public function addHotelPicAction(Request $request){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);

        $picInfo = json_encode(array('description'=>$request->request->get('desripction'),'hotel_id'=>$request->request->get('id')));
        $addPic = $client->request('POST','images', [
            'body' => $picInfo,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $createdPic = json_decode($addPic->getBody()->getContents());

        $dizin = '/var/www/hotel-ui/web/images/';
        $yuklenecek_dosya = $dizin . basename($_FILES['image']['name']);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $yuklenecek_dosya)){
            $response = $client->request('PUT','/images/'.$createdPic->id.'/upload', [
                'body'=>fopen($yuklenecek_dosya,'r')
            ]);
            unlink($yuklenecek_dosya);
            return new JsonResponse( $response->getBody()->getContents());
        }
        else {

            return new JsonResponse("hata");
        }
    }

    /**
     * @Route("/delete-hotel-pic")
     */
    public function deleteHotelPicAction(Request $request){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('DELETE','images/'.$request->get('id'));

        return new JsonResponse($response->getBody()->getContents());
    }

    //-------------------------------------------------------------------------------------

    /**
     * @Route("/getHotelRooms")
     */
    public function getHotelRoomsAction(Request $request){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('GET','hotels/'.$request->request->get('id').'/rooms');

        return new JsonResponse($response->getBody()->getContents());
    }

    /**
     * @Route("/room-index",name="room-index")
     */
    public function roomIndexAction(Request $request){
        return $this->render(':Otel:admin-room-index.html.twig');
    }

    /**
     * @Route("/add-room-index",name="add-room-index")
     */
    public function addRoomIndexAction(){
        return $this->render(':Otel:admin-add-room.html.twig');
    }

    /**
     * @Route("/add-room")
     */
    public function addRoomAction(Request $request){
        $room = json_encode(array(
            "hotel_id"=> $request->request->get('hotelId'),
            "type" => $request->request->get('type'),
            "price" => $request->request->get('price'),
            "floor" => $request->request->get('floor'),
            "detail" =>$request->request->get('detail')
        ));
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('POST','rooms', [
            'body' => $room,
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        return new JsonResponse($response->getBody()->getContents());
    }

    /**
     * @Route("/deleteRoom")
     */
    public function deleteRoomAction(Request $request){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('delete','rooms/'.$request->request->get('id'));
        return new JsonResponse("silindi");
    }

    /**
     * @Route("/edit-room-index",name="edit-room-index")
     */
    public function editRoomIndexAction(){
        return $this->render(':Otel:admin-edit-room.html.twig');
    }

    /**
     * @Route("/getRoom")
     */
    public function getRoomAction(Request $request){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('GET','rooms/'.$request->request->get('id'));

        return new JsonResponse($response->getBody()->getContents());
    }

    /**
     * @Route("/edit-room")
     */
    public function editRoomAction(Request $request){
        $room = json_encode(array(
            "type" => $request->request->get('type'),
            "price" => $request->request->get('price'),
            "detail" => $request->request->get('detail')
        ));

        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('PATCH','rooms/'.$request->request->get('id'), [
            'body' => $room,
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
        return new JsonResponse('başarılı');
    }

    /**
     * @Route("/edit-room-pics",name="edit-room-pics")
     */
    public function editRoomPicsAction(){
        return $this->render(':Otel:admin-room-pic.html.twig');
    }

    /**
     * @Route("/add-room-pic")
     */
    public function addRoomPicAction(Request $request){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);

        $picInfo = json_encode(array('description'=>$request->request->get('desripction'),'hotel_id'=>$request->request->get('hotelId'),'room_id'=>$request->request->get('roomId')));
        $addPic = $client->request('POST','images', [
            'body' => $picInfo,
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ]);

        $createdPic = json_decode($addPic->getBody()->getContents());

        $dizin = '/var/www/hotel-ui/web/images/';
        $yuklenecek_dosya = $dizin . basename($_FILES['image']['name']);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $yuklenecek_dosya)){
            $response = $client->request('PUT','/images/'.$createdPic->id.'/upload', [
                'body'=>fopen($yuklenecek_dosya,'r')
            ]);
            unlink($yuklenecek_dosya);
            return new JsonResponse( $response->getBody()->getContents());
        }
        else {

            return new JsonResponse("hata");
        }
    }

    /**
     * @Route("/get-room-pics")
     */
    public function getRoomPicsAction(Request $request){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('GET','rooms/'.$request->request->get('id').'/images');
        return new JsonResponse($response->getBody()->getContents());
    }

    /**
     * @Route("/delete-room-pic")
     */
    public function deleteRoomPicAction(Request $request){
        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);
        $response = $client->request('DELETE','images/'.$request->get('id'));

        return new JsonResponse($response->getBody()->getContents());
    }
}

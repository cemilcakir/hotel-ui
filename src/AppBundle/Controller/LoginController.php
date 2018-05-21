<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Session\Session;


class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(){
        return $this->render(':Otel:login.html.twig');
    }

    /**
     * @Route("/signup", name="signup")
     */
    public function signupAction(){
        return $this->render(':Otel:sign-up.html.twig');
    }

    /**
     * @Route("/signout", name="signout")
     */
    public function signOutAction(){
        $session = $this->get('session');
        $session->clear();

        $response = $this->forward('AppBundle:Index:index');
        return $response;
    }

    /**
     * @Route("/new-user")
     */
    public function newUserAction(Request $request){
        $client = new Client(['base_uri'=>'localhost:8001/']);

        if ($request->request->get("password") != $request->request->get("repassword"))
            return new JsonResponse("Şifreler uyuşmuyor.");

        $mails = json_decode($client->request("GET","humans")->getBody()->getContents());
        foreach ($mails as $mail){
            if ($mail->mail == $request->request->get("mail"))
                return new JsonResponse("var");
        }

        $person = json_encode(array("first_name"=>$request->request->get("name"),"last_name"=>$request->request->get("last_name"),"kullanici_t_c"=>$request->request->get("kullanici_tc"),"date_of_birth"=>$request->request->get("date_of_birth"),"mail"=>$request->request->get("mail")));

        $response = $client->request('POST','humans',['body'=>$person,
            'headers' => [
                'Content-Type' => 'application/json',
            ]]);
        $personId=json_decode($response->getBody()->getContents())->id;

        $user = json_encode(array("username"=>$request->request->get("mail"),"password"=>$request->request->get("password"),"retyped_password"=>$request->request->get("repassword"),"roles"=>"ROLE_ADMIN","person_id"=>$personId));

        $response = $client->request("POST","users",['body'=>$user,"headers"=>['Content-Type'=>'application/json']]);
        $userId =json_decode($response->getBody()->getContents())->id;

        $response = $client->request("PATCH","humans/".$personId,["body"=>json_encode(array("user_id"=>$userId)),
            'headers' => [
                'Content-Type' => 'application/json',
            ]]);

        return new JsonResponse("success");

    }

    /**
     * @Route("/signin")
     */
    public function signinAction(Request $request){
        $client = new Client(['base_uri'=>'localhost:8001/']);

        $response = $client->request("GET","/users/".$request->request->get("mail")."/id")->getBody()->getContents();
        $person = json_decode($response);
        $userId = json_decode($response)[0]->user_id;

        try {
            $response = $client->request("POST","tokens", [
                'auth' => [
                    $request->request->get("mail"),
                    $request->request->get("password")
                ]
            ]);

            $token = json_decode($response->getBody()->getContents())->token;

            $response = $client->request("GET","users/".$userId,["headers"=>[
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ]]);
            $user = json_decode($response->getBody()->getContents());

            $session = new Session();
            $session->clear();
            $session->start();

            $session->set('userFirstName',$person[0]->first_name);
            $session->set('userLastName',$person[0]->last_name);
            $session->set('userId',$userId);
            $session->set('userTC',$person[0]->kullanici_t_c);
            $session->set('userBirth',$person[0]->date_of_birth);
            $session->set('userMail',$person[0]->mail);
            $session->set('userToken',$token);
            $session->set('userRole',$user->roles[0]);

            return new JsonResponse($person);
        } catch (RequestException $e) {
           return new JsonResponse("fail");
        }
    }
}

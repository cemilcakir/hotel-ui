<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

class UserInfoController extends Controller
{
    /**
     * @Route("/user-info",name="user-info")
     */
    public function indexAction(){
        return $this->render(':Otel:user-info.html.twig');
    }
    /**
    *@Route("/pass-change")
    */
    public function passChangeAcction(Request $request){
        $session = $this->get('session');
        $userId = $session->get('userId');
        $token = $session->get('userToken');

        $password = $request->request->get('newPass');
        $retypedPassword = $request->request->get('rePass');

        $body = json_encode(array("password"=>$password,"retyped_password"=>$retypedPassword));

        $client = new Client(['base_uri'=>$this->container->getParameter('app_bundle.api_link')]);

        $response = $client->request('PATCH','users/'.$userId, [
            'body' => $body,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token
            ]
        ]);

        return new JsonResponse($response->getBody()->getContents());
    }
}

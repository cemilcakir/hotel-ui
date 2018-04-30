<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class GalleryController extends Controller
{
    /**
     * @Route("/gallery",name="gallery")
     */
    public function IndexAction(){
        return $this->render(':Otel:gallery.html.twig');
    }
}

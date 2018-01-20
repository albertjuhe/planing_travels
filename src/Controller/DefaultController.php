<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * Matches /
     * @Route("/{_locale}",defaults={"_locale"="en"},name="homepage")
     *
     * @return Response
     */
    public function index($_locale)
    {
        return $this->render('default/index.html.twig',array('locale'=>$_locale));
    }
}
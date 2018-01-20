<?php
/**
 * Created by PhpStorm.
 * User: Albert
 * Date: 20/01/2018
 * Time: 19:59
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class PrivateController extends Controller
{
    /**
     * @Route("/{_locale}/private",name="main_private")
     *
     * @return Response
     */
    public function index() {
        return $this->render('private/index.html.twig', array());
    }
}
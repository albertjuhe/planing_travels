<?php
namespace App\Controller;

use App\Entity\Travel;
use App\Form\TravelType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TravelController extends Controller
{
    /**
     * @Route("/{_locale}/private/new",name="newTravel")
     * @return Response
     */
    public function newTravel(Request $request,$_locale) {

        $travel = new Travel();
        $travel->setUser($this->getUser());
        $travel->setStars(0);
        $travel->setWatch(0);

        $form = $this->createForm(TravelType::class,$travel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $travel = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($travel);
            $em->flush();

            return $this->redirectToRoute('main_private');
        }

        return $this->render('travel/new.html.twig',[
            'travelForm' => $form->createView()
        ]);
    }
}
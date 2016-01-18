<?php

namespace MMI\Jub\GalerieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class FrontOfficeController extends Controller {
	public function indexAction() {
		$em = $this->getDoctrine ()->getManager ();
		$userRepository = $em->getRepository ( 'MMIJubGalerieBundle:User' );
		$users = $userRepository->findAll ();
		
		return $this->render ( 'MMIJubGalerieBundle:FrontOffice:index.html.twig', array (
				'users' => $users 
		) );
	}
}

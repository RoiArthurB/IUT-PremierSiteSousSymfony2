<?php

namespace MMI\Jub\GalerieBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use MMI\Jub\GalerieBundle\Entity\User;
use Knp\Menu\Renderer\Renderer;
use MMI\Jub\GalerieBundle\Entity\Ci;
use MMI\Jub\GalerieBundle\Entity\Possede;


/**
 * @author groupe9
 * Le contrôleur responsable du BackOffice
 */
class BackOfficeController extends Controller {
		
		
////////////////////////////////////////////////////////////////////////////
//																		  //
//						FONCTION UPLOAD									  //
//																		  //
////////////////////////////////////////////////////////////////////////////
	
	/**
	 * formulaire d'ajout d'un CI
	 *
	 * @param unknown $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function uploadCiListAction($id){
		$user = $this->getUser (); //l'utilisateur courant qui sera la propriétaire du centre d interet
	
		$em = $this->getDoctrine ()->getManager ();
		$possedeRepository = $em->getRepository('MMIJubGalerieBundle:Possede');
		$ciRepository = $em->getRepository('MMIJubGalerieBundle:Ci');
		$ci = $ciRepository->findOneById($id);
	
		//crée la possession
		$possede = new Possede();
		$masque = false;
		$possede->setUser($user);
		$possede->setCiP($ci);
		$possede->setMasque($masque);
	
		//crée l'association
		$em->persist($possede);
		$em->flush();
		return $this->redirect ( $this->generateUrl ( 'mmi_jub_galerie_profil' ) );
	}	
	
	/**
	 * Permet d'afficher la liste de centre d'interet et réaliser l'ajout
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function listeCiAction() {
		$em = $this->getDoctrine()->getManager(); //recupérartion du gestionnaire d'entités
		$ciRepository = $em->getRepository('MMIJubGalerieBundle:Ci'); //récupération du dépot des Ci
	
		$query = $ciRepository->createQueryBuilder('u') //utilisation du constructeur de requêtes
		->getQuery(); //récupération de la requête préparée
			
		$cis_tt = $query->getResult(); //Récupération du résultat de l'exécution de la requête
	
		return $this->render('MMIJubGalerieBundle:BackOffice:liste.html.twig', array (
				'cis_tt' => $cis_tt));  //Affichage de la vue de la liste Ci
	}
	
	/**
	 * Permet d'afficher le formulaire d'ajout d'un centre d'interet
	 * et l'ajoute à la BDD
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function uploadAction() {
		$user = $this->getUser (); //l'utilisateur courant qui sera la propriétaire du centre d interet
		$ci = new Ci(); //le nouveau centre d interet
		$possede = new Possede();
		$masque = false;
		$possede->setUser($user);
		$possede->setCiP($ci);
		$possede->setMasque($masque);
	
		//Envoie le CI ds la BDD et crée la possession
		$form = $this->createFormBuilder ( $ci )->
		add ( 'nomCi', 'text' )->getForm (); //Création de l'objet formulaire avec le champ nomCi
	
		if ($this->getRequest ()->isMethod ( 'POST' )) { //l'on revient du formulaire, nous sommes en POST
				
			$form->handleRequest ( $this->getRequest () ); //Récupération des valeurs du formulaire
				
			if ($form->isValid ()) { //Le formulaire est-il valide
	
				$em = $this->getDoctrine ()->getManager ();
				//le centre d'interet est enregistrée dans la base, le fichier est physiquement sauvegardé en utilisant le cycle de vie de l'entité
				$em->persist ( $ci );
				$em->flush ();
				//crée l'association
				$em->persist($possede);
				$em->flush();
				return $this->redirect ( $this->generateUrl ( 'mmi_jub_galerie_profil' ) );
			}
		}
		return $this->render ( 'MMIJubGalerieBundle:BackOffice:upload.html.twig', array (
				'form' => $form->createView ()
		) );
	}
	
////////////////////////////////////////////////////////////////////////////
//																		  //
//						FONCTION VISIBILITE								  //
//																		  //
////////////////////////////////////////////////////////////////////////////

	/**
	 * Affiche le profil de l'utilisateur courant.
	 *
	 */
	public function profilAction() {
		$user = $this->getUser();
		$roles = $user->getRoles();
		$role="";
		if (in_array('ROLE_ADMIN', $roles)){
			$role = 'ROLE_ADMIN';
		}
	
		return $this->render('MMIJubGalerieBundle:BackOffice:profil.html.twig', array (
				'role' => $role, 'user' => $user ));
	}
	
	/**
	 * Montre les ci de l'utilisateur courant
	 *
	 * @param unknown $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showGalerieAction($id){
		$em = $this->getDoctrine()->getManager();
		$possedeRepository = $em->getRepository('MMIJubGalerieBundle:Possede');
		$userRepository = $em->getRepository('MMIJubGalerieBundle:User');
		$user = $userRepository->findOneById($id);
			
		$possede= $possedeRepository->findByUser($user);
	
		return $this->render ( 'MMIJubGalerieBundle:BackOffice:cis.html.twig', array (
				'possede' => $possede
		)
		);
	}
	
	///////////////////////////////Gestion de sa visibilite

	/**
	 * Modifie la visibilité d'un centre d'intérêt
	 *
	 * @param unknow $masque
	 * @param integer $id
	 */
	public function masqueCiAction ($id, $masque) {
		//crée les repository
		$em = $this->getDoctrine()->getManager();
		$possedeRepository = $em->getRepository('MMIJubGalerieBundle:Possede');
		$ciRepository = $em->getRepository('MMIJubGalerieBundle:Ci');
		//retrouve l'utilisateur et le ci ds la BDD
		$user = $this->getUser(); //récup l'id de l'utilisateur courant
		$ci = $ciRepository->findOneById($id);
		$possede = new Possede();
	
		if ($masque == 0 || $masque == false){
			$masqueChangement = true;
		};
		if ($masque == 1 || $masque == true){
			$masqueChangement = false;
		}
		$possede = $possedeRepository->updateMasque($ci, $user, $masqueChangement);
	
		$em->flush();
	
		return $this->redirect($this->generateUrl('mmi_jub_galerie_profil'));
	}

	
	///////////////////////////////Voir les autres
	
	/**
	 * Permet d'afficher la liste de centre d'interet
	 * et de chercher les gens le partageant
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function listeRechercheCiAction() {
		$em = $this->getDoctrine()->getManager(); //recupérartion du gestionnaire d'entités
		$ciRepository = $em->getRepository('MMIJubGalerieBundle:Ci'); //récupération du dépot des Ci
	
		$query = $ciRepository->createQueryBuilder('u') //utilisation du constructeur de requêtes
		->getQuery(); //récupération de la requête préparée
			
		$cis_tt = $query->getResult(); //Récupération du résultat de l'exécution de la requête
	
		return $this->render('MMIJubGalerieBundle:BackOffice:listeRecherche.html.twig', array (
				'cis_tt' => $cis_tt));  //Affichage de la vue de la liste Ci
	}
	
	/**
	 * Montre les centres d'interets des autres utilisateurs que le courant
	 *
	 * @param unknown $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function showGalerieAutreAction($id){
		$em = $this->getDoctrine()->getManager();
		$possedeRepository = $em->getRepository('MMIJubGalerieBundle:Possede');
		$userRepository = $em->getRepository('MMIJubGalerieBundle:User');
		$user = $userRepository->findOneById($id);
			
		$possede= $possedeRepository->findByUser($user);
		return $this->render ( 'MMIJubGalerieBundle:BackOffice:cisAutre.html.twig', array (
				'possede' => $possede,
				'user' => $user)
		);
	}

	/**
	 * Affiche les utilisateurs à partir d'un CI
	 *
	 * @param unknown $id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function rechercheCiAction($id){
		$em = $this->getDoctrine()->getManager();
		$possedeRepository = $em->getRepository('MMIJubGalerieBundle:Possede');
		$ciRepository = $em->getRepository('MMIJubGalerieBundle:Ci');
		$ci = $ciRepository->findOneById($id);
			
		$possede= $possedeRepository->findByCiP($ci);
	
		return $this->render ( 'MMIJubGalerieBundle:BackOffice:resultatRechercheCi.html.twig',
				array ('possede' => $possede)
		);
	}
	
////////////////////////////////////////////////////////////////////////////
//																		  //
//							FONCTION ADMINISTRATIF						  //
//																		  //
////////////////////////////////////////////////////////////////////////////	
	
	/**
	 * Affiche le compte d'un utilisateur.
	 * Si l'utilisateur est un administrateur, les comptes des autres utilisateurs seront affichés aussi
	 *
	 */
	public function indexAction() {
		$user = $this->getUser(); //Récupére l'utilisateur courant
		$roles = $user->getRoles(); //récupére les rôles de l'utilisateur courant
		$role="";
		if (in_array('ROLE_ADMIN', $roles)){
			$role = 'ROLE_ADMIN'; //Si l'utilisateur courant dispose du rôle ROLE_ADMIN alors la variable $role vaut "ROLE_ADMIN".
		}
	
		return $this->render('MMIJubGalerieBundle:BackOffice:index.html.twig', array (
				'role' => $role, 'user' => $user ));
	}
	
	/**
	 * supprime l'association d'un CI à l'utilisateur courant
	 *
	 * @param unknown $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteCiAction($id){
		//crée les repository
		$em = $this->getDoctrine()->getManager();
		$possedeRepository = $em->getRepository('MMIJubGalerieBundle:Possede');
		$ciRepository = $em->getRepository('MMIJubGalerieBundle:Ci');
		//retrouve l'utilisateur et le ci ds la BDD
		$user = $this->getUser(); //récup l'id de l'utilisateur courant
		$ci = $ciRepository->findOneById($id);
	
		$possede = $possedeRepository->findByIdCiAndIdUserDel($ci, $user);
	
		return $this->redirect($this->generateUrl('mmi_jub_galerie_profil'));
	}
	
////////////////////////////////////////////////////////////////////////////
//																		  //
//							FONCTION ADMIN								  //
//																		  //
////////////////////////////////////////////////////////////////////////////	
	
	/**
	 * FONCTION ADMIN
	 * supprime un CI de la BDD
	 *
	 * @param unknown $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteCiListAction($id){
		$em = $this->getDoctrine ()->getManager ();
		$ciRepository = $em->getRepository('MMIJubGalerieBundle:Ci');
		$ci = $ciRepository->findOneById($id);
		$em->remove($ci);
		$em->flush();
		return $this->redirect ( $this->generateUrl ( 'mmi_jub_galerie_profil' ) );
	}

	/**
	 * FONCTION ADMIN
	 * Permet d'afficher la liste de centre d'interet et réaliser la suppression
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function listeCiDeleteAction() {
		$em = $this->getDoctrine()->getManager(); //recupérartion du gestionnaire d'entités
		$ciRepository = $em->getRepository('MMIJubGalerieBundle:Ci'); //récupération du dépot des Ci
	
		$query = $ciRepository->createQueryBuilder('u') //utilisation du constructeur de requêtes
		->getQuery(); //récupération de la requête préparée
			
		$cis_tt = $query->getResult(); //Récupération du résultat de l'exécution de la requête
	
		return $this->render('MMIJubGalerieBundle:BackOffice:listedelete.html.twig', array (
				'cis_tt' => $cis_tt));  //Affichage de la vue de la liste Ci
	}
	
	/**
	 * FONCTION ADMIN
	 * supprime un utilisateur de la BDD
	 *
	 * @param unknown $id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function deleteUserAction($id){
		$em = $this->getDoctrine()->getManager();
		$userRepository = $em->getRepository('MMIJubGalerieBundle:User');
		$user= $userRepository->findOneBy(array('id'=>$id));
		$em->remove($user);
		$em->flush();
		return $this->redirect($this->generateUrl('mmi_jub_galerie_admin'));
	}

	/**
	 * FONCTION ADMIN
	 * Permet de voir les utilisateurs uniquement
	 *
	 * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function showUsersAction(){
		if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())){ //Permet de savoir si l'utilisateur est un administrateur
			$em = $this->getDoctrine ()->getManager (); //recupérartion du gestionnaire d'entités
			$userRepository = $em->getRepository('MMIJubGalerieBundle:User'); //récupération du dépot de User
			$username = $this->getUser()->getUserName(); //Récupération du nom de l'utilisateur courant
			$query = $userRepository->createQueryBuilder('u') //utilisation du constructeur de requêtes
			->where('u.username <> :username') //on ne garde que les utilisateurs différents de l'utilisateur courant
			->setParameter('username', $username) //:username est un paramètre, on le remplace par le nom de l'utilisateur courant.
			->getQuery(); //récupération de la requête préparée
				
			$users = $query->getResult(); //Récupération du résultat de l'exécution de la requête
				
				
			return $this->render('MMIJubGalerieBundle:BackOffice:users.html.twig', array (
					'users' => $users)); } //Affichage de la vue de gestion utilisateurs
			else
				return $this->redirect ( $this->generateUrl ( 'mmi_jub_galerie_admin' ) ); //Si pas ADMIN alors redirection vers l'accueil de l'administration
	}
}
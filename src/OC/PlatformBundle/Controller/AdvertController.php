<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Annonce;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Image;

class AdvertController extends Controller
{
  public function indexAction()
  {
      // On récupère le repository
      $em = $this->getDoctrine()
          ->getManager();


      // On récupère l'entité correspondante à l'id $id
      $listAdverts = $em->getRepository('OCPlatformBundle:Annonce')->findBy(
          array(), // Critere
          array('date' => 'desc'),        // Tri
          3,                              // Limite
          0                               // Offset
      );
      $image[] = null ;
      $i=0;
      foreach ($listAdverts as $advert)
      {
          $image = $em->getRepository('OCPlatformBundle:Image')->findOneBy(
              array('advert' => $advert)
          );

          $advert->setUrlImage($image->getUrl());
      }



      return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
  'listAdverts' => $listAdverts ));
  }

  public function viewAction($id)
  {
      // On récupère le repository
      $em = $this->getDoctrine()
          ->getManager();


      // On récupère l'entité correspondante à l'id $id
      $advert = $em->getRepository('OCPlatformBundle:Annonce')->find($id);

      // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
      // ou null si l'id $id  n'existe pas, d'où ce if :
      if (null === $advert) {
          throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
      }

      $listeImage = $em->getRepository('OCPlatformBundle:Image')->findBy(array('advert'=>$advert));
      // Le render ne change pas, on passait avant un tableau, maintenant un objet
      return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
          'advert' => $advert,
          'listeImages' => $listeImage
      ));
  }

  public function addAction(Request $request)
  {
      // Création de l'entité
      $advert = new Annonce();
      $advert->setTitre('Appartement 2 chambres meublé moderne à Palmeraie');
      $advert->setAuteur('Mohamed');
      $advert->setPhraseType("Cet appartement offre une vue magnifique sur la plage!");
      $advert->setDescription("L'agence Belimar Immo met en location longue durée uniquement un bel appartement Sur le circuits de la palmeraie dans une résidence sécurisée avec piscine commerce à proximité. Composé de :

2 chambres
1 sdb
1 toilette invite
balcon
Cuisine entièrement équipée");
      $advert->setPrix(3500);
      // On peut ne pas définir ni la date ni la publication,
      // car ces attributs sont définis automatiquement dans le constructeur

      //image 1:
      $image = new Image();
      $image->setUrl('assets/images/App1/appartement-2.jpg');
      $image->setAdvert($advert);


      //image 2:
      $image2 = new Image();
      $image2->setUrl('assets/images/App1/appartement-a-louer-1.jpg');
      $image2->setAdvert($advert);

      //image 3:
      $image3 = new Image();
      $image3->setUrl('assets/images/App1/appartements-brussel-18.jpg');
      $image3->setAdvert($advert);

      //image 4:
      $image4 = new Image();
      $image4->setUrl('assets/images/App1/appartements-brussel-22.jpg');
      $image4->setAdvert($advert);


      // On récupère l'EntityManager
      $em = $this->getDoctrine()->getManager();

      // Étape 1 : On « persiste » l'entité
      $em->persist($advert);
      $em->persist($image);
      $em->persist($image2);
      $em->persist($image3);
      $em->persist($image4);

      // Étape 2 : On « flush » tout ce qui a été persisté avant
      $em->flush();

      // Reste de la méthode qu'on avait déjà écrit
      if ($request->isMethod('POST')) {
          $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

          // Puis on redirige vers la page de visualisation de cettte annonce
          return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
      }

      // Si on n'est pas en POST, alors on affiche le formulaire
      return $this->render('OCPlatformBundle:Advert:add.html.twig', array('advert' => $advert));
  }

  public function editAction($id, Request $request)
  {
        // ...
    
    $advert = array(
      'title'   => 'Recherche développpeur Symfony',
      'id'      => $id,
      'author'  => 'Alexandre',
      'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
      'date'    => new \Datetime()
    );

    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
      'advert' => $advert
    ));
  }

  public function deleteAction($id)
  {
    // Ici, on récupérera l'annonce correspondant à $id

    // Ici, on gérera la suppression de l'annonce en question

    return $this->render('OCPlatformBundle:Advert:delete.html.twig');
  }
  
  public function emailAction($pseudo,$mail)
  {
	  $contenu = $this->renderView('OCPlatformBundle:Advert:email.html.twig',array('pseudo' => $pseudo));
	  
	  mail($mail,'Inscription OK', $contenu);
  }
  
    public function menuAction()
  {
    // On fixe en dur une liste ici, bien entendu par la suite
    // on la récupérera depuis la BDD !
    $listAdverts = array(
      array('id' => 2, 'title' => 'Appartement 2 chambres meublé à Semlalia'),
      array('id' => 5, 'title' => 'Appartement 2 chambres meublé à Palmeraie'),
      array('id' => 9, 'title' => 'Appartement 2 chambres meublé moderne à Palmeraie')
    );

    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      // Tout l'intérêt est ici : le contrôleur passe
      // les variables nécessaires au template !
      'listAdverts' => $listAdverts
    ));
  }

  public function annoncesAction ($page)
  {
      // On ne sait pas combien de pages il y a
      // Mais on sait qu'une page doit être supérieure ou égale à 1
      if ($page < 1) {
          // On déclenche une exception NotFoundHttpException, cela va afficher
          // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
          throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
      }

      // Ici, on récupérera la liste des annonces, puis on la passera au template

      // ...

      // Notre liste d'annonce en dur
      // On récupère le repository
      $em = $this->getDoctrine()
          ->getManager();


      // On récupère l'entité correspondante à l'id $id
      $listAdverts = $em->getRepository('OCPlatformBundle:Annonce')->findAll();
      $image[] = null ;
      $i=0;
      foreach ($listAdverts as $advert)
      {
          $image = $em->getRepository('OCPlatformBundle:Image')->findOneBy(
              array('advert' => $advert)
          );

          $advert->setUrlImage($image->getUrl());
      }



      return $this->render('OCPlatformBundle:Advert:annonces.html.twig', array(
          'listAdverts' => $listAdverts ));
  }

  public function loginAction()
  {
      return $this->render('OCPlatformBundle:Advert:login.html.twig');
  }

}
?>
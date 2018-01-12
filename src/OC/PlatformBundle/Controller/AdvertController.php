<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use OC\PlatformBundle\Entity\Document;
use OC\PlatformBundle\Entity\Images;
use OC\PlatformBundle\Entity\Myfile;
use OC\PlatformBundle\Form\MyfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Image;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type;
use OC\PlatformBundle\Entity\Annonce;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use OC\UserBundle\Entity\User;
class AdvertController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        // On récupère le repository
        $em = $this->getDoctrine()
            ->getManager();


        // On récupère les annonces les plus récentes

        $listAdverts = $em->getRepository('OCPlatformBundle:Annonce')->findBy(
            array('publier' => 1), // Critere
            array('date' => 'desc'),        // Tri
            6,                              // Limite
            0                               // Offset
        );
        $image[] = null;
        foreach ($listAdverts as $advert) {
            $image = $em->getRepository('OCPlatformBundle:Images')->findOneBy(
                array('annonce' => $advert)
            );

            $advert->setImagePrincipale($image->getPath());
        }


        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts));
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
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        $listeImage = $em->getRepository('OCPlatformBundle:Images')->findBy(array('annonce' => $advert));
        // Le render ne change pas, on passait avant un tableau, maintenant un objet
        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
            'advert' => $advert,
            'listeImages' => $listeImage
        ));
    }

    public function addAction(Request $request)
    {
        // On crée un objet Advert
        $advert = new Annonce();

        // J'ai raccourci cette partie, car c'est plus rapide à écrire !
        $form = $this->get('form.factory')->createBuilder(FormType::class, $advert)
            ->add('type', ChoiceType::class, array(
                'choices' => array(
                    'Appartement' => 'Appartement',
                    'Chambre' => 'Chambre',
                    'Colocation' => 'Colocation',
                ),
            ))
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    'A Louer' => 'A Louer',
                    'A vendre' => 'A vendre',

                ),
            ))
            ->add('quelEtage', ChoiceType::class, array(
                'choices' => array(
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,

                ),
            ))
            ->add('nbrChambres', ChoiceType::class, array(
                'choices' => array(
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,

                ),
            ))
            ->add('nbrToilettes', ChoiceType::class, array(
                'choices' => array(
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,

                ),
            ))
            ->add('ville', ChoiceType::class, array(
                'choices' => array(
                    'Marrakech' => 'Marrakech',
                    'Casablanca' => 'Casablanca',
                    'Rabat' => 'Rabat',
                    'Agadir' => 'Agadir',

                ),
            ))
            ->add('region', ChoiceType::class, array(
                'choices' => array(
                    'Gueliz' => 'Gueliz',
                    'Daoudiate' => 'Daoudiate',
                    'Assif' => 'Assif',
                    'Saada' => 'Saada',
                    'Sidi abbad' => 'Sidi abbad',
                    'Issil' => 'Issil',

                ),
            ))
            ->add('universite', ChoiceType::class, array(
                'choices' => array(
                    'FST' => 'FST',
                    'ENSA' => 'ENSA',
                    'SEMLALIA' => 'SEMLALIA',
                    'FAC de droit' => 'FAC de droit',

                ),
            ))
            ->add('titre', TextType::class)
            ->add('description', TextareaType::class)
            ->add('addresse', TextType::class)
            ->add('prix', MoneyType::class, array(
                'currency' => false
            ))
            ->add('ascenceur', CheckboxType::class, array('required' => false))
            ->add('meuble', CheckboxType::class)
            ->add('parcking', CheckboxType::class)
            ->add('chauffe', CheckboxType::class)
            ->add('uploadedFiles', FileType::class, array(
                'multiple' => true
            ))
            ->getForm();

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            // (Nous verrons la validation des objets en détail dans le prochain chapitre)
            if ($form->isValid()) {

                // On récupère l'EntityManager
                $em = $this->getDoctrine()->getManager();

                foreach ($advert->getUploadedFiles() as $uploadedFile) {
                    $image = new Images();

                    /*
                     * These lines could be moved to the File Class constructor to factorize
                     * the File initialization and thus allow other classes to own Files
                     */
                    $path = sha1(uniqid(mt_rand(), true)) . '.' . $uploadedFile->guessExtension();
                    $image->setPath($path);
                    $image->setSize($uploadedFile->getClientSize());
                    $image->setName($uploadedFile->getClientOriginalName());
                    $imageName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
                    $image->setPath('uploads/images/' . $imageName);

                    $uploadedFile->move($this->getParameter('brochures_directory'), $imageName);

                    $advert->getImages()->add($image);
                    $image->setAnnonce($advert);
                    $em->persist($image);

                    unset($uploadedFile);
                }

                $advert->setUser($this->getUser());
                $em->persist($advert);
                $em->flush();

                // On redirige vers la page de visualisation de l'annonce nouvellement créée
                return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView(),
        ));


    }




    public function emailAction($pseudo, $mail)
    {
        $contenu = $this->renderView('OCPlatformBundle:Advert:email.html.twig', array('pseudo' => $pseudo));

        mail($mail, 'Inscription OK', $contenu);
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

    public function annoncesAction($page)
    {
        // On récupère le repository
        $em = $this->getDoctrine()
            ->getManager();

        $listAdverts = $em->getRepository('OCPlatformBundle:Annonce')->findBy(
            array('publier' => 1)
        );
        foreach ($listAdverts as $advert) {
            $image = $em->getRepository('OCPlatformBundle:Images')->findOneBy(
                array('annonce' => $advert)
            );

            $advert->setImagePrincipale($image->getPath());
        }
        return $this->render('OCPlatformBundle:Advert:annonces.html.twig', array('listAdvert' => $listAdverts));
    }

    public function annoncesappartementsAction($page)
    {
        // On récupère le repository
        $em = $this->getDoctrine()
            ->getManager();

        $listAdverts = $em->getRepository('OCPlatformBundle:Annonce')->findBy(array("type" => "Appartement"));
        foreach ($listAdverts as $advert) {
            $image = $em->getRepository('OCPlatformBundle:Images')->findOneBy(
                array('annonce' => $advert)
            );

            $advert->setImagePrincipale($image->getPath());
        }
        return $this->render('OCPlatformBundle:Advert:annonces.html.twig', array('listAdvert' => $listAdverts));
    }

    public function annonceschambresAction($page)
    {
        // On récupère le repository
        $em = $this->getDoctrine()
            ->getManager();

        $listAdverts = $em->getRepository('OCPlatformBundle:Annonce')->findBy(array("type" => "Chambre"));
        foreach ($listAdverts as $advert) {
            $image = $em->getRepository('OCPlatformBundle:Images')->findOneBy(
                array('annonce' => $advert)
            );

            $advert->setImagePrincipale($image->getPath());
        }
        return $this->render('OCPlatformBundle:Advert:annonces.html.twig', array('listAdvert' => $listAdverts));
    }

    public function annoncescolocationAction($page)
    {
        // On récupère le repository
        $em = $this->getDoctrine()
            ->getManager();

        $listAdverts = $em->getRepository('OCPlatformBundle:Annonce')->findBy(array("type" => "Colocation"));
        foreach ($listAdverts as $advert) {
            $image = $em->getRepository('OCPlatformBundle:Images')->findOneBy(
                array('annonce' => $advert)
            );

            $advert->setImagePrincipale($image->getPath());
        }
        return $this->render('OCPlatformBundle:Advert:annonces.html.twig', array('listAdvert' => $listAdverts));
    }

    public function loginAction()
    {
        return $this->render('OCPlatformBundle:Advert:login.html.twig');
    }

    public function testAction()
    {
        return $this->render('OCPlatformBundle:Advert:test.html.twig');
    }

    public function annoncesgridAction($page)
    {
        return $this->render('OCPlatformBundle:Advert:annoncesgrid.html.twig');
    }

    public function contactAction()
    {
        return $this->render('OCPlatformBundle:Advert:contact.html.twig');
    }

    public function aboutAction()
    {
        return $this->render('OCPlatformBundle:Advert:about.html.twig');
    }

    public function blogAction()
    {
        return $this->render('OCPlatformBundle:Advert:blog.html.twig');
    }

    public function adminAction()
    {
        // On récupère le repository
        $em = $this->getDoctrine()
            ->getManager();

        $listAdverts = $em->getRepository('OCPlatformBundle:Annonce')->findBy(array("publier" => 0));
        foreach ($listAdverts as $advert) {
            $image = $em->getRepository('OCPlatformBundle:Images')->findOneBy(
                array('annonce' => $advert)
            );

            $advert->setImagePrincipale($image->getPath());
        }
        return $this->render('OCPlatformBundle:Advert:annonces.html.twig', array('listAdvert' => $listAdverts));
    }

    public function activeAction($id)
    {
        // On récupère le repository
        $em = $this->getDoctrine()
            ->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Annonce')->find($id);
        $advert->setPublier(1);
        $em->persist($advert);
        $em->flush();
        return $this->redirectToRoute('oc_platform_admin');
    }

    public function profileAction()
    {
        return $this->render('OCPlatformBundle:Advert:profile.html.twig');
    }

    public function propertiesAction()
    {
        // On récupère le repository
        $em = $this->getDoctrine()
            ->getManager();

        $listAdverts = $em->getRepository('OCPlatformBundle:Annonce')->findBy(array("user" => $this->getUser()));
        foreach ($listAdverts as $advert) {
            $image = $em->getRepository('OCPlatformBundle:Images')->findOneBy(
                array('annonce' => $advert)
            );

            $advert->setImagePrincipale($image->getPath());
        }

        return $this->render('OCPlatformBundle:Advert:myproperties.html.twig', array('listAdverts' => $listAdverts, 'user' => $this->getUser()));
    }


    public function editAction(Request $request, Annonce $advert)
    {
        // J'ai raccourci cette partie, car c'est plus rapide à écrire !
        $form = $this->get('form.factory')->createBuilder(FormType::class, $advert)
            ->add('type', ChoiceType::class, array(
                'choices' => array(
                    'Appartement' => 'Appartement',
                    'Chambre' => 'Chambre',
                    'Colocation' => 'Colocation',
                ),
            ))
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    'A Louer' => 'A Louer',
                    'A vendre' => 'A vendre',

                ),
            ))
            ->add('quelEtage', ChoiceType::class, array(
                'choices' => array(
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,

                ),
            ))
            ->add('nbrChambres', ChoiceType::class, array(
                'choices' => array(
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,

                ),
            ))
            ->add('nbrToilettes', ChoiceType::class, array(
                'choices' => array(
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,

                ),
            ))
            ->add('ville', ChoiceType::class, array(
                'choices' => array(
                    'Marrakech' => 'Marrakech',
                    'Casablanca' => 'Casablanca',
                    'Rabat' => 'Rabat',
                    'Agadir' => 'Agadir',

                ),
            ))
            ->add('region', ChoiceType::class, array(
                'choices' => array(
                    'Gueliz' => 'Gueliz',
                    'Daoudiate' => 'Daoudiate',
                    'Assif' => 'Assif',
                    'Saada' => 'Saada',
                    'Sidi abbad' => 'Sidi abbad',
                    'Issil' => 'Issil',

                ),
            ))
            ->add('universite', ChoiceType::class, array(
                'choices' => array(
                    'FST' => 'FST',
                    'ENSA' => 'ENSA',
                    'SEMLALIA' => 'SEMLALIA',
                    'FAC de droit' => 'FAC de droit',

                ),
            ))
            ->add('titre', TextType::class)
            ->add('description', TextareaType::class)
            ->add('addresse', TextType::class)
            ->add('prix', MoneyType::class, array(
                'currency' => false
            ))
            ->add('ascenceur', CheckboxType::class, array('required' => false))
            ->add('meuble', CheckboxType::class)
            ->add('parcking', CheckboxType::class)
            ->add('chauffe', CheckboxType::class)
            ->add('uploadedFiles', FileType::class, array(
                'multiple' => true
            ))
            ->getForm();

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            // (Nous verrons la validation des objets en détail dans le prochain chapitre)
            if ($form->isValid()) {

                // On récupère l'EntityManager
                $em = $this->getDoctrine()->getManager();

                foreach ($advert->getUploadedFiles() as $uploadedFile) {
                    $image = new Images();

                    /*
                     * These lines could be moved to the File Class constructor to factorize
                     * the File initialization and thus allow other classes to own Files
                     */
                    $path = sha1(uniqid(mt_rand(), true)) . '.' . $uploadedFile->guessExtension();
                    $image->setPath($path);
                    $image->setSize($uploadedFile->getClientSize());
                    $image->setName($uploadedFile->getClientOriginalName());
                    $imageName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
                    $image->setPath('uploads/images/' . $imageName);

                    $uploadedFile->move($this->getParameter('brochures_directory'), $imageName);

                    $advert->getImages()->add($image);
                    $image->setAnnonce($advert);
                    $em->persist($image);

                    unset($uploadedFile);
                }

                $advert->setUser($this->getUser());
                $em->persist($advert);
                $em->flush();

                // On redirige vers la page de visualisation de l'annonce nouvellement créée
                return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
            }
        }
        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView(),
        ));

    }
    public function deleteAction(Annonce $advert)
    {
        $em = $this->getDoctrine()->getManager();
        $listImages = $em->getRepository('OCPlatformBundle:Images')->findBy(array("annonce" => $advert));
        foreach ($listImages as $image) {
            $em->remove($image);
        }
        $em->remove($advert);
        $em->flush();

        // On récupère le repository
        $em = $this->getDoctrine()
            ->getManager();

        $listAdverts = $em->getRepository('OCPlatformBundle:Annonce')->findBy(array("user" => $this->getUser()));
        foreach ($listAdverts as $advert) {
            $image = $em->getRepository('OCPlatformBundle:Images')->findOneBy(
                array('annonce' => $advert)
            );

            $advert->setImagePrincipale($image->getPath());
        }

        return $this->render('OCPlatformBundle:Advert:myproperties.html.twig', array('listAdverts' => $listAdverts, 'user' => $this->getUser()));

    }
}
?>
<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Service\Helpers;
use App\Form\PersonneType;
use App\Service\PdfService;
use Psr\Log\LoggerInterface;
use App\Service\MailerService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('personne'), IsGranted('ROLE_USER')]
class PersonneController extends AbstractController
{
    public function __construct(
        private LoggerInterface $logger,
        private Helpers $helper,
        private EventDispatcherInterface $dispatcher
    )
    {}
    
#[Route('/', name:'personne.list')]
function index(ManagerRegistry $doctrine): Response
    {

    $repository = $doctrine->getRepository(Personne::class);
    $personnes = $repository->findAll();
    return $this->render('personne/index.html.twig', [
        'personnes' => $personnes,
    ]);
}

#[Route('/pdf/{id}', name:'personne.pdf')]
public function generatePdfPersonne(Personne $personne = null, PdfService $pdf): Response
{
    $html = $this->render('personne/detail.html.twig', [
        'personne' => $personne,
    ]);
    $pdf->showPdfFile($html);
}

#[Route('/alls/age/{ageMin}/{ageMax}', name:'personne.age')]
function personnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
    $repository = $doctrine->getRepository(Personne::class);
    $personnes = $repository->findPersonneByAgeInterval($ageMin, $ageMax);
    return $this->render('personne/index.html.twig', [
        'personnes' => $personnes,
    ]);
}

#[Route('/stats/age/{ageMin}/{ageMax}', name:'personne.stats')]
function statsPersonnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
    $repository = $doctrine->getRepository(Personne::class);
    $stats = $repository->statsPersonnesByAgeInterval($ageMin, $ageMax);
    return $this->render('personne/stats.html.twig', [
        'stats' => $stats[0],
        'ageMin' => $ageMin,
        'ageMax' => $ageMax]
    );
}

#[
    Route('/alls/{page?1}/{nbre?12}', name:'personne.alls'), 
    IsGranted("ROLE_USER"),
    ]
function indexAlls(ManagerRegistry $doctrine, $page, $nbre): Response
    {
       // echo($helper->azizCisse());

    $repository = $doctrine->getRepository(Personne::class);
    $nbPersonne = $repository->count([]);
    $nbrePage = ceil($nbPersonne / $nbre);
    $personnes = $repository->findBy([], [], $nbre, offset:($page - 1) * $nbre, );
    return $this->render('personne/index.html.twig', [
        'personnes' => $personnes,
        'isPaginated' => true,
        'nbrePage' => $nbrePage,
        'page' => $page,
        'nbre' => $nbre,
    ]);
}

#[Route('/{id<\d+>}', name:'personne.detail')]
function detail(Personne $personne = null): Response
    {
    if (!$personne) {
        $this->addFlash(
            'error',
            "La personne n'existe pas."
        );
        return $this->redirectToRoute('personne.list');
    }
    return $this->render('personne/detail.html.twig', [
        'personne' => $personne,
    ]);
}

#[Route('/add', name:'personne.add')]
function addPersonne(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
    $personne = new Personne;
    //$personne est l'image de notre formulaire
    $form = $this->createForm(PersonneType::class, $personne);
    $form->remove('createdAt');
    $form->remove('updatedAt');
    // Mon formulaire va aller traiter la requete
    $form->handleRequest($request);
    //Est ce que le formulaire a été soumis
    if ($form->isSubmitted() && $form->isValid()) {
        // si oui,
        // on va ajouter l'objet personne dans la base de données
        $photo = $form->get('photo')->getData();
        if ($photo) {
            $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $photo->guessExtension();
            try {
                $photo->move(
                    $this->getParameter('personne_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
            }
            $personne->setImage($newFilename);
        }

        $manager = $doctrine->getManager();
        $manager->persist($personne);
        $manager->flush();
        // Afficher un message de succès
        $this->addFlash("success", "La personne a été ajouté avec succès");
        // Rediriger verts la liste des personne
        return $this->redirectToRoute('personne.list');
    } else {
        //Sinon
        //On affiche notre formulaire
    }
    return $this->render('personne/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/edit/{id?0}', name:'personne.edit')]
function editPersonne(
    Personne $personne = null, 
    ManagerRegistry $doctrine, 
    Request $request,
    UploaderService $uploaderService,
    MailerService $mailer,
    ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    $new = false;
    if (!$personne) {
        $new = true;
        $personne = new Personne();
    }
    //$personne est l'image de notre formulaire
    $form = $this->createForm(PersonneType::class, $personne);
    $form->remove('createdAt');
    $form->remove('updatedAt');
    // Mon formulaire va aller traiter la requete
    $form->handleRequest($request);
    //Est ce que le formulaire a été soumis
    if ($form->isSubmitted() && $form->isValid()) {
        // si oui,
        // on va ajouter l'objet personne dans la base de données
        $photo = $form->get('photo')->getData();
        if ($photo) {
            $directory = $this->getParameter('personne_directory');
            $personne->setImage($uploaderService->uploadFile($photo, $directory));
        }
        // Afficher un message de succès  
        if ($new) {
            $message = " a été ajouté avec succès";
            $personne->setCreatedBy($this->getUser());
        } else {
            $message = " a été mis à jour avec succès";
            
        }  
        // Afficher un message de succès
        $manager = $doctrine->getManager();
        $manager->persist($personne);
        $manager->flush();
         
        $mailMessage = $personne->getFirstname().'  '.$personne->getName().' '.$message;
        // Afficher un message de succès
        $this->addFlash('success', $personne->getName() . $message);
        $mailer->sendEmail(content: $mailMessage);
        // Rediriger verts la liste des personne
        return $this->redirectToRoute('personne.list');
    } else {
        //Sinon
        //On affiche notre formulaire
    }
    return $this->render('personne/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/delete/{id}', name:'personne.delete'), IsGranted("ROLE_ADMIN")]
function deletePersonne(ManagerRegistry $doctrine, Personne $personne = null): Response
    {
    //Récupérer la personne
    if ($personne) {
        $manager = $doctrine->getManager();
        //Ajouter la fonction de suppression dans la transaction
        $manager->remove($personne);
        //Executer la transaction
        $manager->flush();
        //Si la personne existe => le supprimer et retourner un flashMessage de succès
        $this->addFlash(
            'success',
            "La personne est bien supprimée."
        );
    } else {
        //Si non retourner un flashMessage d'erreur
        $this->addFlash(
            'error',
            "La personne n'existe pas."
        );
    }
    return $this->redirectToRoute('personne.alls');
    return $this->render('personne/delete.html.twig', [

    ]);
}

#[Route('/update/{id}/{firstname}/{name}/{age}', name:'personne.update')]
function updatePersonne(ManagerRegistry $doctrine, Personne $personne = null, $firstname, $name, $age): Response
    {
    //Vérifier que la peronne à mettre à jour existe
    if ($personne) {
        //Si la personne existe => mettre à jour notre personne + un flashMessage de succès.
        $personne->setFirstname($firstname);
        $personne->setName($name);
        $personne->setAge($age);
        $manager = $doctrine->getManager();
        $manager->persist($personne);
        $manager->flush();
        $this->addFlash(
            'success',
            'La personne a été mis à jour avec succès'
        );
    } else {
        //Si non => Déclencher un flashMessage d'erreu
        $this->addFlash(
            'error',
            "' personne n'existe pas."
        );
    }
    return $this->redirectToRoute('personne.alls');

    return $this->render('personne/update.html.twig', []);
}
}

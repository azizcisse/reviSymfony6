<?php

namespace App\Controller;

use App\Entity\Personne;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine): Response
    {

        $repository = $doctrine->getRepository(Personne::class); 
        $personnes = $repository->findAll();
        return $this->render('personne/index.html.twig', [
                  'personnes' => $personnes,
        ]);
    }

    #[Route('/alls/age/{ageMin}/{ageMax}', name: 'personne.age')]
    public function personnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {

        $repository = $doctrine->getRepository(Personne::class); 
        $personnes = $repository->findPersonneByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/index.html.twig', [
                  'personnes' => $personnes,
        ]);
    }

   
    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.stats')]
    public function statsPersonnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonnesByAgeInterval($ageMin, $ageMax);
        return $this->render('personne/stats.html.twig', [
            'stats' => $stats[0],
            'ageMin'=> $ageMin,
            'ageMax' => $ageMax]
        );
    }
    #[Route('/alls/{page?1}/{nbre?12}', name: 'personne.alls')]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre): Response
    {

        $repository = $doctrine->getRepository(Personne::class); 
        $nbPersonne = $repository->count([]);
        $nbrePage = ceil($nbPersonne / $nbre);
        $personnes = $repository->findBy([], [], $nbre, offset:($page - 1) * $nbre,);
        return $this->render('personne/index.html.twig', [
                  'personnes' => $personnes,
                  'isPaginated' => true,
                  'nbrePage' => $nbrePage,
                  'page' => $page,
                  'nbre' => $nbre
        ]);
    }

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(Personne $personne = null): Response
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


    #[Route('/add', name: 'personne.add')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $personne = new Personne;
        

        $entityManager->persist($personne);
        $entityManager->flush();

        return $this->render('personne/detail.html.twig', [
            'personne' => $personne,
        ]);
    }


    #[Route('/delete/{id}', name: 'personne.delete')]
    public function deletePersonne(ManagerRegistry $doctrine, Personne $personne = null): Response
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
        }
         else {
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

    #[Route('/update/{id}/{firstname}/{name}/{age}', name: 'personne.update')]
    public function updatePersonne(ManagerRegistry $doctrine, Personne $personne = null, $firstname, $name, $age): Response
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
          }else {
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

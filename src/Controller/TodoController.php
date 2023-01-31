<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/todo")]
class TodoController extends AbstractController
{
    #[Route('/', name:'app_todo')]
function index(Request $request): Response
    {
    $session = $request->getSession();
    //Affichage de notre tableau de Todo
    //Sinon je l'initialise puis je l'affiche
    if (!$session->has('todos')) {
        $todos = [
            'Achat' => 'Acheter Une Clée USB',
            'Cours' => 'Finaliser Mon Cours',
            'Correction' => 'Corriger Mes Examens',
        ];
        $session->set('todos', $todos);
        $this->addFlash(
           'info',
           "La liste des todos vient d'etre iniltialisée"
        );
    }
    //Si j'ai mon tableau de todo dans ma session je ne fais que l'afficher
    
    return $this->render('todo/index.html.twig');
}
 #[Route('/add/{name}/{content}', name: 'add.todo', defaults: ['name' => 'Aziz', 'content' => 'Cisse'])]
  public function addTodo(Request $request, $name, $content): Response
  {
    $session = $request->getSession();
    //Vérifier mon tableau de todo dans la session
    if ($session->has('todos')) {
         //Si oui
       //Vérifier Si on a déjà un todo avec le meme nom
       $todos = $session->get('todos');
       if (isset($todos[$name])) {
        //Si Oui afficher erreur
        $this->addFlash(
            'error',
            "Le todo d'id $name existe déjà dans la liste"
         );
       }
       else {
            // Si non on l'ajoute et on affiche un message de succès
            $todos[$name] = $content;
            $this->addFlash(
                'success',
                "Le todo d'id $name a été ajouté avec succès"
             );
             $session->set('todos', $todos);
       }
        
    }
    else {
          //Si non
        //Afficher une erreur et rediriger vers le controlleur index
        $this->addFlash(
            'error',
            "La liste des todos n'est pas encore iniltialisée"
         );
    }
      return $this->redirectToRoute('app_todo');  
      //return $this->render('todo/add.html.twig');
  }  
  #[Route('/update/{name}/{content}', name: 'update.todo')]
  public function updateTodo(Request $request, $name, $content): Response
  {
    $session = $request->getSession();
    //Vérifier mon tableau de todo dans la session
    if ($session->has('todos')) {
         //Si oui
       //Vérifier Si on a déjà un todo avec le meme nom
       $todos = $session->get('todos');
       if (!isset($todos[$name])) {
        //Si Oui afficher erreur
        $this->addFlash(
            'error',
            "Le todo d'id $name n'existe pas dans la liste"
         );
       }
       else {
            // Si non on l'ajoute et on affiche un message de succès
            $todos[$name] = $content;
            $this->addFlash(
                'success',
                "Le todo d'id $name a été modifié avec succès"
             );
             $session->set('todos', $todos);
       }
        
    }
    else {
          //Si non
        //Afficher une erreur et rediriger vers le controlleur index
        $this->addFlash(
            'error',
            "La liste des todos n'est pas encore iniltialisée"
         );
    }
      return $this->redirectToRoute('app_todo');  
      //return $this->render('todo/add.html.twig');
  } 
  #[Route('/delete/{name}', name: 'delete.todo')]
  public function deleteTodo(Request $request, $name): Response
  {
    $session = $request->getSession();
    //Vérifier mon tableau de todo dans la session
    if ($session->has('todos')) {
         //Si oui
       //Vérifier Si on a déjà un todo avec le meme nom
       $todos = $session->get('todos');
       if (!isset($todos[$name])) {
        //Si Oui afficher erreur
        $this->addFlash(
            'error',
            "Le todo d'id $name n'existe pas dans la liste"
         );
       }
       else {
            // Si non on l'ajoute et on affiche un message de succès
            unset($todos[$name]);
            $session->set('todos', $todos);
            $this->addFlash(
                'success',
                "Le todo d'id $name a été supprimé avec succès"
             );
            
       }
        
    }
    else {
          //Si non
        //Afficher une erreur et rediriger vers le controlleur index
        $this->addFlash(
            'error',
            "La liste des todos n'est pas encore iniltialisée"
         );
    }
      return $this->redirectToRoute('app_todo');  
      //return $this->render('todo/add.html.twig');
  }  
  #[Route('/reset', name: 'reset.todo')]
  public function resetTodo(Request $request): Response
  {
    $session = $request->getSession();
    $session->remove('todos');
           
      return $this->redirectToRoute('app_todo');  
      //return $this->render('todo/add.html.twig');
  }  
}

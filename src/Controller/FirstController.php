<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    #[Route('/template', name:'template')]
function template()
    {
    return $this->render('template.html.twig');
}

#[Route('/order/{maVar}', name:'test.order.route')]
function testOrderRoute($maVar)
    {
    return new Response(" <html><body>$maVar</body></html>");
}

#[Route('/first', name:'app_first')]
function index(): Response
    {
    //Pour chercher au nveau de la BD nos utilisateurs
    return $this->render('first/index.html.twig', [
        'firstname' => 'Aziz',
        'name' => 'Cisse',
    ]);
}

//#[Route('/aziz/{firstname}/{name}', name:'app_aziz')]
function hello(Request $request, $name, $firstname): Response
    {

    return $this->render('first/hello.html.twig', [
        'prenom' => $firstname,
        'nom' => $name,

    ]);
}

#[Route("multi/{entier1<\d+>}/{entier2<\d+>}", name:"multiplication")]

function multiplication($entier1, $entier2): Response
    {
    $resultat = $entier1 * $entier2;
    return new Response("<h1>$resultat</h1>");
}
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\From;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
//permet la connexion de user
    #[Route('/connexion', name: 'security.login',methods:['GET','POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
      
        return $this->render('pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error'=> $authenticationUtils->getLastAuthenticationError()
        ]);
    }
//deconnexuion d un user
    #[Route('/deconnexion',name:'security.logout')]
    public function logout(){
        //nothing to do here
    }
//iscription dun user
    #[Route('/inscription',name:'security.registration',methods:['GET','POST'])]
    public function registration(Request $request,EntityManagerInterface $manager):Response{

        $user= new User();
        $user->setRoles(['ROLE_USER']);
        $form=$this->createForm(RegistrationType::class,$user );

        $form->handleRequest($request);
        //dd($form->getData());
        if($form->isSubmitted() && $form->isValid()){
            $user=$form->getData();
            

            $this->addFlash(
                'success',
                'votre compte a bien ete cree !'
            );
            $manager->persist($user);
            $manager->flush();
            return $this-> redirectToRoute('security.login');
        }
        return $this->render('pages/security/registration.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}

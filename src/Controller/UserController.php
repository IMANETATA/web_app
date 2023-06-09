<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/user/edition/{id}', name: 'user.edit')]
    public function edit(User $user,Request $request,EntityManagerInterface $manager,UserPasswordHasherInterface $hasher): Response
    {
        if( !$this->getUser()){
            return $this-> redirectToRoute('security.login');
        }

        if($this->getUser() !== $user){
            return $this-> redirectToRoute('recipe.index');
        }

        $form = $this->createForm(UserType::class,$user );
        $form= $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
           // dd($form->getData());
            if($hasher->isPasswordValid($user,$form->getData()->getPlainPassword())){
                $user= $form->getData();
                $manager->persist($user);
                $manager->flush();
                
                $this->addFlash(
                    'success',
                    'votre compte a bien ete modifié !'
                );
            return $this->redirectToRoute('recipe.index');
            }else{
                $this->addFlash(
                    'warning',
                    'le mot de passe renseigné est incorrecte !'
                );
            }
            }
        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/user/edit-pwd/{id}',name:'user.edit.pwd',methods:['GET','POST'])]
    public function editPassword(User $user,Request $request,UserPasswordHasherInterface $hasher,EntityManagerInterface $manager):Response{

        $form= $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($hasher->isPasswordValid($user,$form->getData()['plainPassword'])){
                $user->setPassword( $hasher->hashPassword($user,$form->getData()['newPassword']));
                //dd($user); 
                $manager->persist($user);
                $manager->flush();
                
                $this->addFlash(
                    'success',
                    'le mot de passe a ete bien modifie !'
                );
                return $this->redirectToRoute('recipe.index');
            }else{
                $this->addFlash(
                    'warning',
                    'le mot de passe est incorrect !'
                );
              
            }
        }
        return $this->render('pages/user/edit_password.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}

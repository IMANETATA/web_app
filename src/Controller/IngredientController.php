<?php

namespace App\Controller;

//use App\Controller\Task;
use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
//use App\Entity\Ingredient;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    /**
     * this CONTROLLER display all ingredients
     * 
     *  @param IngredientRepository $repository
     *  @param PaginatorInterface $paginator
     *  @param Request $request
     *  @return Reponse
     */

    #[Route('/ingredient', name: 'ingredient.index',methods:['GET'])]


    public function index(IngredientRepository $repository,PaginatorInterface $paginator,Request $request): Response
    {
        $Ingredients=$paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page',1),
            10
        );
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients'=>$Ingredients
        ]);
    }
    /**
     * this controller permet d ajouter ingredients
     * 
     *  @param IngredientRepository $repository
     *  @param PaginatorInterface $paginator
     *  @param Request $request
     *  @return Reponse
     */
    #[Route('/ingredient/nouveau','ingredient.new',methods:['GET','POST'])]


    public function new(Request $request,EntityManagerInterface $manager): Response {
        $ingredient = new Ingredient();
        $form= $this->createForm(IngredientType::class,$ingredient);
        $form->handleRequest($request);
        
        if($form->isSubmitted() &&  $form->isValid() ){
            $ingredient=$form->getData();
            
            $manager->persist($ingredient);//ajout a la base
           $manager->flush();
           $this->addFlash(
            'success',
            'votre ingredient a ete cree avec success!'
        );
        return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/new.html.twig',[
            'form' => $form ->createView()
        ]);
    }
    /**
     * this controller permet de modifier ingredients
     * 
     *  @param IngredientRepository $repository
     *  @param PaginatorInterface $paginator
     *  @param Request $request
     *  @return Reponse
     */
#[Route('ingredient/edition/{id}',name:'ingredient.edit',methods:['GET','POST'])]

    public function edit(Ingredient $ingredient,Request $request,EntityManagerInterface $manager):Response{
       
        $form= $this->createForm(IngredientType::class,$ingredient);
        $form->handleRequest($request);
        
        if($form->isSubmitted() &&  $form->isValid() ){
            $ingredient=$form->getData();
            
            $manager->persist($ingredient);//ajout a la base
           $manager->flush();
           $this->addFlash(
            'success',
            'votre ingredient a ete modifié avec success!'
        );
        return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/edit.html.twig',[
            'form'=>  $form->createView()
        ]);
    }
#[Route('/ingredient/supression/{id}',name:'ingredient.delete',methods:['GET'])]
    public function delete(EntityManagerInterface $manager,Ingredient $ingredient):Response {
        if(!$ingredient){
            $this->addFlash(
                'warning',
                'votre ingredient n a pas ete trouvé!'
            );
            return $this->redirectToRoute('ingredient.index');

        }
        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash(
            'success',
            'votre ingredient a ete supprimé avec success!'
        );
        return $this->redirectToRoute('ingredient.index');
    }
}
<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * this controller permet d afficher recettes
     * 
     *  @param RecipeRepository $repository
     *  @param PaginatorInterface $paginator
     *  @param Request $request
     *  @return Reponse
     */
    #[Route('/recette', name: 'recipe.index')]
    #[IsGranted('ROLE_USER')]
    public function index(RecipeRepository $repository,PaginatorInterface $paginator,Request $request): Response
    {
        $recipes=$paginator->paginate(
            $repository->findBy(['user'=>$this->getUser()]),
            $request->query->getInt('page',1),
            10
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }


    //
    #[Route('/recipe/public',name:'recipe.index.public',methods:['GET'])]
     public function indexPublic(PaginatorInterface $paginator,RecipeRepository $repository,Request $request):Response{

        $recipes=$paginator->paginate($repository->findPublicRecipe(null),
        $request->query->getInt('page',1),
        10
    );

        return $this->render('pages/recipe/indexPublic.html.twig',[
            'recipes'=>$recipes
        ]);
     }
   
//voir la recette si elle est publique
    //#[Security("is_granted('ROLE_USER')and recipe.isPublic === true")]
    #[Route('/recipe/{id}',name:'recipe.show',methods:['GET','POST'])]
    public function show(Recipe $recipe,Request $request,MarkRepository $markRepository,EntityManagerInterface $manager): Response{
        $mark=new Mark();
        $form=$this->createForm(MarkType::class,$mark);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $mark->setUser($this->getUser())
            ->setRecipe($recipe);
            $existingMark=$markRepository->findOneBy([
                'user'=> $this->getUser(),
                'recipe'=>$recipe
            ]);
            if(!$existingMark ){
                $manager->persist($mark);
            }
            else{
             $existingMark->setMark(
                $form->getData()->getMark()
             );
           
            }
            $manager->flush();
            $this->addFlash(
               'success',
               'votre note a bien ete prise en compte'
            );
            return $this->redirectToRoute('recipe.show',[
                'id'=>$recipe->getId()
            ]);
            
        }

        return $this-> render('pages/recipe/show.html.twig',[
            'recipe'=>$recipe,
            'form'=>$form->createView()
        ]);
    }

    /**
     * this controller permet de modifier une recettes
     * 
     *  @param RecipeRepository $repository
     *  @param PaginatorInterface $paginator
     *  @param Request $request
     *  @return Reponse
     */
    #[Route('/recette/nouveau',name:'recipe.new',methods:['GET','POST'])]
    public function new(Request $request,EntityManagerInterface $manager) :Response{

        $recipe = new Recipe();
        $form = $this->createForm( RecipeType::class , $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $recipe= $form-> getData();
            $recipe->setUser($this->getUser());
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash(
                'success',
                'votre recette a ete cree avec success!'
            );
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/new.html.twig',[
            'form' => $form->createView()
        ]);
    }
     /**
     * this controller permet de modifier une recettes
     * 
     *  @param RecipeRepository $repository
     *  @param PaginatorInterface $paginator
     *  @param Request $request
     *  @return Reponse
     */
    #[Route('recette/edition/{id}',name:'recipe.edit',methods:['GET','POST'])]

    public function edit(Recipe $recipe,Request $request,EntityManagerInterface $manager):Response{
       
        $form= $this->createForm(RecipeType::class,$recipe);
        $form->handleRequest($request);
        
        if($form->isSubmitted() &&  $form->isValid() ){
            $recipe=$form->getData();
            
            $manager->persist($recipe);//ajout a la base
           $manager->flush();
           $this->addFlash(
            'success',
            'votre recette a été modifiée avec success!'
        );
        return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/edit.html.twig',[
            'form'=>  $form->createView()
        ]);
    }

    #[Route('/recette/supression/{id}',name:'recipe.delete',methods:['GET'])]
    public function delete(EntityManagerInterface $manager,Recipe $recipe):Response {
        if(!$recipe){
            $this->addFlash(
                'warning',
                'votre recette n a pas ete trouvée!'
            );
            return $this->redirectToRoute('recipe.index');

        }
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash(
            'success',
            'votre recette a ete supprimée avec success!'
        );
        return $this->redirectToRoute('recipe.index');
    }
}

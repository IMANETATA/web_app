<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Ingredient;
use Faker\Provider\ar_EG\Text;
use App\Repository\IngredientRepository;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\AbstractType;
//use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class RecipeType extends AbstractType
{
    //recuperer current user
    private $token;
    /*public function __construct(TokenStorageInterface $token)
    {
        $this->token=$token;
    }*/
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name',TextType::class,[
            'attr'=>[
                'class'=>'form-control',
                'minlength'=>'2',
                'maxlength'=>'50'
            ],
            'label'=>'Nom',
            'label_attr'=>[
                'class'=>'form-label mt-4'
            ],
            'constraints'=> [
                new Assert\Length(['min'=>2,'max'=>50]),
                new Assert\NotBlank()
                ]
                ])
            ->add('time',IntegerType::class,[
                'attr'=>[
                    'class'=>'form-control mt-4',
                    'minlength'=> 1,
                    'maxlength'=> 1440
                ],
                'label'=>'temps (en minutes)',
                'label_attr'=>[
                    'class'=>'form-label mt-4'
                ],
                'constraints'=>[
                  new  Assert\LessThan(1441),
                   new Assert\Positive()
                ]
            ])
            ->add('nbPeople',IntegerType::class,[
                'attr'=>[
                    'class'=>'form-control',
                    'minlength'=> 1,
                    'maxlength'=> 50
                ],
                'label'=>'nombre de personne',
                'label_attr'=>[
                    'class'=>'form-label mt-4'
                ],
                'constraints'=>[
                  new  Assert\LessThan(51),
                   new Assert\Positive()
                ]
            ])
            ->add('difficulty',RangeType::class,[
                'attr'=>[
                    'class'=>'form-range',
                    'min'=> 1,
                    'max'=> 5
                ],
                'label'=>'difficulté',
                'label_attr'=>[
                    'class'=>'form-label mt-4'
                ],
                'constraints'=>[
                  new  Assert\LessThan(6),
                   new Assert\Positive()
                ]
            ])
            ->add('description',TextareaType::class,[
                'attr'=>[
                    'class'=>'form-control',
                    'min'=> 1,
                    'max'=> 5
                ],
                'label'=>'description',
                'label_attr'=>[
                    'class'=>'form-label mt-4'
                ],
                'constraints'=>[
                  new Assert\NotBlank()
                ]
            ])
            ->add('price',MoneyType::class,[
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label'=>'Prix',
                'label_attr'=>[
                    'class'=>'form-label mt-4'
                ],
                'constraints'=> [
                    new Assert\Positive(),
                    new Assert\LessThan(1001)
                ]
            ])
            ->add('isFavorite',CheckboxType::class,[
                'attr'=>[
                    'class'=>'form-check-input'
                ],
                'label'=>'favoris ?',
                'label_attr'=>[
                    'class'=>'form-check-label '
                ],
                'constraints'=> [
                    new Assert\NotNull()
                ]
            ])
            ->add('ingredients',EntityType::class,[
                
                'class'=> Ingredient::class,
                'choice_label'=>'name',
                'multiple'=>true,
                'query_builder'=> function(IngredientRepository $r){
                    return $r->createQueryBuilder('i')
                    //->where('i.user=:user')
                    ->orderBy('i.name','ASC');
                  //  ->setParameter('user',$this->token->getToken()->getUser());
                },
                'expanded'=>true,
                'label'=>'les  ingredients',
                'label_attr'=>[
                    'class'=>'form-label mt-4'
                ],
            ])
            ->add('submit',SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-primary mt-4'
                ],
                'label'=>'creer ma recette'
                   
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}

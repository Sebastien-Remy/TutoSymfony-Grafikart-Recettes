<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{

    public function __construct(private readonly FormListenerFactory $formListenerFactory)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('slug')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('content')
            ->add('duration')
            ->add('save', SubmitType::class )
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->formListenerFactory->autoslug('title'))
            ->addEventListener(FormEvents::POST_SUBMIT, $this->formListenerFactory->timestamps())
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}

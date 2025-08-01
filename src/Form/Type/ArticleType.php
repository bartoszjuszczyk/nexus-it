<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'Title',
            'required' => true,
        ])->add('content', TextareaType::class, [
            'label' => 'Content',
            'required' => true,
            'attr' => ['rows' => 20],
        ])->add('categories', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
            'label' => 'Categories',
            'multiple' => true,
            'expanded' => false,
            'autocomplete' => true,
        ])->add('save', SubmitType::class, [
            'label' => 'Save',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}

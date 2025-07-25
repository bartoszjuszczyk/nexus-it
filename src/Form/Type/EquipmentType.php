<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EquipmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'Name',
            'required' => true,
        ])->add('inventoryNumber', TextType::class, [
            'label' => 'Inventory number',
            'required' => true,
        ])->add('assignedTo', EntityType::class, [
            'class' => User::class,
            'label' => 'Assigned to',
            'required' => false,
            'choice_label' => 'fullname',
            'placeholder' => 'Choose user...',
        ])->add('save', SubmitType::class, [
            'label' => 'Save',
        ]);
    }
}

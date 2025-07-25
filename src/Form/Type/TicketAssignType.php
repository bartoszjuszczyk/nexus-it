<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketAssignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('assignedWorker', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'fullname',
            'label' => 'Assign specialist',
            'required' => true,
            'placeholder' => 'Choose specialist...',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->andWhere('u.roles LIKE :support_role')
                    ->orWhere('u.roles LIKE :admin_role')
                    ->setParameter('support_role', '%"ROLE_SUPPORT"%')
                    ->setParameter('admin_role', '%"ROLE_ADMIN"%');
            },
        ])->add('save', SubmitType::class, [
            'label' => 'Assign',
            'attr' => [
                'class' => 'btn-primary w-100',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}

<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Ticket\TicketStatus;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketFilterType extends AbstractType
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('status', EntityType::class, [
            'class' => TicketStatus::class,
            'choice_label' => 'label',
            'label' => 'Status',
            'required' => false,
            'placeholder' => 'Choose a status...',
        ]);
        if ($this->security->isGranted('ROLE_SUPPORT')) {
            $builder->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstname',
                'label' => 'Author',
                'required' => false,
                'placeholder' => 'Choose a user...',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->innerJoin('u.tickets', 't', Join::WITH, 't.author = u')
                        ->groupBy('u.id');
                },
            ]);
        }
        $builder->add('filter', SubmitType::class, [
            'label' => 'Filter',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}

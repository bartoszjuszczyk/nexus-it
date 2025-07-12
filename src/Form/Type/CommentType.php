<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class CommentType extends AbstractType
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('comment', TextareaType::class, [
            'label' => 'Comment',
            'attr' => ['rows' => 4],
        ])
            ->add('attachments', FileType::class, [
                'label' => 'Attachments',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new All([
                        new File([
                            'maxSize' => '5120k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'application/pdf',
                                'application/zip',
                            ],
                            'mimeTypesMessage' => 'Send file in valid format (JPG, PNG, PDF, ZIP).',
                        ]),
                    ]),
                ],
            ]);
        if ($this->security->isGranted('ROLE_SUPPORT')) {
            $builder->add('is_internal', CheckboxType::class, [
                'label' => 'Internal comment',
                'mapped' => false,
                'required' => false,
            ]);
        }
        $builder->add('save', SubmitType::class, [
            'label' => 'Add',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}

<?php

/**
 * File: UserType.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'First Name',
                'required' => false,
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Last Name',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'disabled' => true,
                'label' => 'Email',
                'required' => false,
            ])
            ->add('avatar', FileType::class, [
                'mapped' => false,
                'label' => 'Choose image',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please use JPG, PNG or GIF format image.',
                    ]),
                ],
                'help' => 'JPG, PNG or GIF. Maximum 2MB.',
            ])
            ->add('locale', LocaleType::class, [
                'label' => 'Locale',
                'required' => false,
            ])
            ->add('timezone', TimezoneType::class, [
                'label' => 'Timezone',
                'required' => false,
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

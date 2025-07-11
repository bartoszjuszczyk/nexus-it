<?php

/**
 * File: UserType.php.
 *
 * @author Bartosz Juszczyk <b.juszczyk@bjuszczyk.pl>
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;

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
//            ->add('plainPassword', RepeatedType::class, [
//                'type' => PasswordType::class,
//                'invalid_message' => 'The password fields must match.',
//                'first_options' => ['label' => 'New Password', 'hash_property_path' => 'password'],
//                'second_options' => ['label' => 'Repeat Password'],
//                'mapped' => false,
//            ])
            ->add('avatar', FileType::class, [
                'mapped' => false,
                'label' => 'Avatar',
                'required' => false,
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
}

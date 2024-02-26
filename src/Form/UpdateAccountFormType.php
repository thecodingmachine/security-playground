<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UpdateAccountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un prénom',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un nom',
                    ]),
                    new Length([
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'constraints' => array(
                    new Length(['max' => 8, 'maxMessage' => 'The password must not contain more than 8 characters']),
                    new Regex('/^[a-zA-Z0-9]/', 'Password should only contain letters & numbers.')
                ),
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Password Confirmation'],
                'invalid_message' => 'Password mismatch.'
            ])
            ->add('idCard', FileType::class, [
                'label' => "Carte d'identité (PDF)",
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

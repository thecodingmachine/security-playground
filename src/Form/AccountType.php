<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,  ['required' => false])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'constraints' => array(
                    new Length(['max' => 8, 'maxMessage' => 'The password must not contain more than 8 characters']),
                    new Regex('/^[a-zA-Z0-9]/', 'Password should only container letters & numbers.')
                ),
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Password Confirmation'],
                'invalid_message' => 'Password mismatch.'
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

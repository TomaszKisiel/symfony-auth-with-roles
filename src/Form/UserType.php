<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add( 'roles', ChoiceType::class, [
                'choices' => [ 'ROLE_ADMIN' => 'ROLE_ADMIN', 'ROLE_USER' => 'ROLE_USER' ],
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'empty_data' => ["ROLE_USER"]
            ] )
            ->add('password', PasswordType::class)
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('age', NumberType::class)
        ;

        $builder->get( 'roles' )
            ->addModelTransformer(
                new CallbackTransformer(
                    function ( $rolesArray ) {
                        return count( $rolesArray ) ? $rolesArray[ 0 ] : null;
                    },
                    function ( $rolesString ) {
                        return [ $rolesString ];
                    }
                ) );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }
}

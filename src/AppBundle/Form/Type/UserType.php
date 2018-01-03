<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('password', TextType::class, [
                    'required' => false
                ]) 
                ->add('username', TextType::class, [
                    'required' => false
                ])
                ->add('phone', TextType::class, [
                    'required' => false,
                ])
                ->add('enabled', TextType::class, [
                    'required' => false,
                    'empty_data' => 0,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\User',
            'allow_extra_fields' => true,
        ]);
    }

    public function getName() {
        return 'username';
    }

}

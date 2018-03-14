<?php

namespace CG\PortfolioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use CG\PortfolioBundle\Entity\User;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', TextType::class, array("label" => "Nom"))
                ->add('email', TextType::class, array("label" => "Adresse e-mail"))
                ->add('password', TextType::class, array("label" => "Mot de passe"))
                ->add('admin', null, array("label" => "Est administrateur ?"))
                ->add('submit', SubmitType::class, array("label" => "Ajouter"));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cg_portfoliobundle_user';
    }


}

<?php

namespace CG\PortfolioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class EducationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('school', TextType::class, array("label" => "Nom de l'école ou université :"))
                ->add('logoSrc', TextType::class,  array("label" => "URL du logo de l'école :"))
                ->add('country', TextType::class, array("label" => "Pays :"))
                ->add('city', TextType::class, array("label" => "Ville :"))
                ->add('degree', IntegerType::class, array("label" => "Niveau de formation (Bac +) :"))
                ->add('startDate', DateType::class, array("label" => "Date de début:", 'widget' => 'single_text'))
                ->add('endDate', DateType::class, array("label" => "Date de fin:", 'widget' => 'single_text'))
                ->add('title', TextType::class, array("label" => "Intitulé du diplôme:"))
                ->add('content', CKEditorType::class, array("label" => "Contenu de la formation :"))
                ->add('submit', SubmitType::class, array("label" => "Ajouter"));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CG\PortfolioBundle\Entity\Education'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cg_portfoliobundle_education';
    }


}

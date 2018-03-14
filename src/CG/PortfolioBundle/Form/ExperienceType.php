<?php

namespace CG\PortfolioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use CG\PortfolioBundle\Entity\Skill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class ExperienceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('company', TextType::class, array('label' => 'Entreprise'))
                ->add('companyLogo', TextType::class, array('label' => 'URL logo entreprise'))
               // ->add('mission', TextareaType::class, array('label' => 'Missions confiées'))
                ->add('startDate', DateType::class, array('label' => 'Date de début', 'widget' => 'single_text'))
                ->add('endDate', DateType::class, array('label' => 'Date de fin', 'widget' => 'single_text'))
                ->add('contractType', ChoiceType::class, array(
                        'choices' => array(
                            'CDD' => 'CDD',
                            'CDI' => 'CDI',
                            'Stage' => 'Stage',
                            'Alternance' => 'Alternance',
                            'Autre' => 'Autre'
                        ),
                        'choices_as_values' => true,
                    )      
                )
                ->add('skills', EntityType::class, array(
                    'class' => Skill::class,
                    'choice_label' => 'name',
                    'multiple' => true,
                    'expanded' => false,
                    'label' => "Technologies utilisées"
                ))
                ->add('mission', CKEditorType::class, array(
                    'label' => 'Missions'
                ))
                ->add('submit', SubmitType::class, array("label" => "Modifier"));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CG\PortfolioBundle\Entity\Experience'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cg_portfoliobundle_experience';
    }


}

<?php

namespace CG\PortfolioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use CG\PortfolioBundle\Entity\Skill;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class SkillType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array("label" => "Nom du skill"))
            ->add('comment', CKEditorType::class, array(
                "label" => "Contexte d'utilisation"
            ))
            ->add('logoSrc', TextType::class, array("label" => "URL/Logo du skill"))
            ->add('level', IntegerType::class, array("label" => "Niveau (0 à 100)"))
            ->add('isSoftware', null, array("label" => "Est-ce un logiciel ?"))
            ->add('submit', SubmitType::class, array("label" => "Modifier"));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Skill::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cg_portfoliobundle_skill';
    }
}

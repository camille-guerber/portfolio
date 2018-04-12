<?php

namespace CG\PortfolioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use CG\PortfolioBundle\Entity\Keyword;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class ArticleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, array("label" => "Titre de l'article"))
            ->add('logo', TextType::class, array("label" => "URL logo de l'article"))
            ->add('content', CKEditorType::class, array(
                "label" => "Et l'article !",
                "config"=>array(
                    "uiColor"=>"#ffffff",
                )
            ))
            ->add('keywords', EntityType::class, array(
                'class' => Keyword::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
                'label' => "Mots-Clés"
            ))
            ->add('submit', SubmitType::class, array("label" => "Ajouter l'article"));   
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CG\PortfolioBundle\Entity\Article'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cg_portfoliobundle_article';
    }
}
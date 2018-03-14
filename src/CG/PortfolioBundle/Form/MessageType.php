<?php

namespace CG\PortfolioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class MessageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'Votre nom :'))
            ->add('email', TextType::class, array('label' => 'Votre adresse mail :'))
            ->add('message', CKEditorType::class, array('label' => 'Et votre message :', 'config' => array(
                'uiColor' => '#ffffff',
                'language' => 'fr',
                'toolbar' => 
                [ 
                    [ 'Bold','Italic','Underline', 'Blockquote','Subscript','Superscript','-','RemoveFormat' ],
                    [ 'NumberedList','BulletedList','-','Outdent','Indent','-','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ],
                    [ 'Styles', 'Format','Font','FontSize' ],
                    [ 'TextColor','BGColor' ],
                    [ 'RemoveFormat' ],
                    [ 'Maximize' ]
                ]
            )))
            ->add('envoyer', SubmitType::class, array("label" => "Envoyer !"));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CG\PortfolioBundle\Entity\Message'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cg_portfoliobundle_message';
    }


}

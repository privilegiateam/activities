<?php
namespace Acme\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('username')
            ->add('firstname', new TextType(), array(
                'label_format' => 'Nom :',
                'required' => true
            ))
            ->add('lastname', new TextType(), array(
                'label_format' => 'Prénom :',
                'required' => true
            ))
            ->add('dateBirth', new TextType(), array(
                'label_format' => 'Date de naissance :'
            ))

//            ->add('gender', new ChoiceType, array(
//                'label_format' => 'Sexe :',
//                'choices' => array(
//                    'm' => 'Homme',
//                    'f' => 'Femme'
//                ),
//                'required' => true
//            ))
//            ->add('phone', new TextType(), array(
//                'label_format' => 'Tél :',
//            ))
            /*->add('image', new FileType(), array(
                'label_format' => 'Image de profile :',
            ))*/
//            ->add('bio', new TextareaType(), array(
//                'label_format' => 'Bio :',
//            ))
                ;
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}

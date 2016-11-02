<?php

namespace Acme\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SettingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pMessages', null, array( 'required' => false))
            ->add('sMessages', null, array( 'required' => false))
            ->add('eMessages', null, array( 'required' => false))
            ->add('pReservations', null, array( 'required' => false))
            ->add('sReservations', null, array( 'required' => false))
            ->add('eReservations', null, array( 'required' => false))
            ->add('pEvents', null, array( 'required' => false))
            ->add('sEvents', null, array( 'required' => false))
            ->add('eEvents', null, array( 'required' => false))
            ->add('pInvitations', null, array( 'required' => false))
            ->add('sInvitations', null, array( 'required' => false))
            ->add('eInvitations', null, array( 'required' => false))
            ->add('pAvis', null, array( 'required' => false))
            ->add('sAvis', null, array( 'required' => false))
            ->add('eAvis', null, array( 'required' => false))
                ;
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Acme\UserBundle\Entity\Setting'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'acme_userbundle_setting';
    }
}

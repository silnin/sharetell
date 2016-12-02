<?php

namespace Silnin\OAuthBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorizeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'allowAccess',
            CheckboxType::class,
            array(
                'label' => 'Allow access',
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Silnin\OAuthBundle\Form\Model\Authorize',
        ));
    }

    public function getName()
    {
        return 'silnin_oauth_authorize';
    }
}

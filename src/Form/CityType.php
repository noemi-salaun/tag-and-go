<?php

namespace App\Form;

use App\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CityType
 *
 * @author Noémi Salaün <noemi.salaun@gmail.com>
 */
class CityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('latitude')
            ->add('longitude')
            ->add('activated')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => City::class,
            'empty_data' => function(FormInterface $form) {
                return new City(
                    $form->get('name')->getData(),
                    $form->get('latitude')->getData(),
                    $form->get('longitude')->getData(),
                    $form->get('activated')->getData()
                );
            }
        ]);
    }
}

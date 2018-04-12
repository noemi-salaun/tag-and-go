<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StationType
 *
 * @author Noémi Salaün <noemi.salaun@gmail.com>
 */
class StationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => function(City $city) {
                    return $city->getId() . ' - ' . $city->getName();
                }
            ])
            ->add('name')
            ->add('address')
            ->add('latitude')
            ->add('longitude')
            ->add('bikesCapacity')
            ->add('bikesAvailable')
            ->add('activated')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Station::class,
            'empty_data' => function(FormInterface $form) {
                return new Station(
                    $form->get('city')->getData(),
                    new \DateTime(),
                    $form->get('name')->getData(),
                    $form->get('address')->getData(),
                    $form->get('latitude')->getData(),
                    $form->get('longitude')->getData(),
                    $form->get('bikesCapacity')->getData(),
                    $form->get('bikesAvailable')->getData(),
                    $form->get('activated')->getData()
                );
            }
        ]);
    }
}

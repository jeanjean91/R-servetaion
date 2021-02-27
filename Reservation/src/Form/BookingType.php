<?php

namespace App\Form;

use App\Entity\Booking;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('beginAt',DateTimeType::class,['date_widget' => 'single_text'])
            ->add('endAt',DateTimeType::class,['date_widget' => 'single_text'])
            ->add('title')
            ->add('description')
            ->add('all_day')
            ->add('background_color', ColorType::class)
            ->add('border_color', ColorType::class)
            ->add('text_color', ColorType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}

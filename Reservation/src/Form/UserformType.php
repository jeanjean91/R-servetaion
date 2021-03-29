<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\Choice;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
class UserformType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',HiddenType::class)


            ->add('password',HiddenType::class);

        $roles = array(
            "User"=>"ROLE_USER",
            "prestataire"=>"ROLE_PRESTA",
            "Admin"=>"ROLE_ADMIN");
        $builder
            ->add('roles', ChoiceType::class, array(
                'choices'  => $roles,
                'multiple' => true,
                'expanded'=>true,
                'mapped'=>true,
                'label' => 'form.roles',
                'translation_domain' => 'messages'
            ))
        ;



    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

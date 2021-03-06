<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Damien\RecaptchaBundle\Type\RecaptchaSubmitType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('object', TextType::class)
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => "ta-contact-form"
                ]
            ])
            /*->add('captcha', RecaptchaSubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn-primary rounded'
                ]
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => Contact::class,
            'translation_domain' => 'forms'
        ]);
    }
}

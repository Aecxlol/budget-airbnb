<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration("Titre de l'annonce"))
            ->add('price', MoneyType::class, $this->getConfiguration("Prix du bien pour la nuit"))
            ->add('content', TextareaType::class, [
                'attr' => [
                    'placeholder' => "Description détaillée de votre bien ...",
                    'class' => "ta-ad-form"
                ]
            ])
            ->add('imageFile', VichImageType::class, $this->getConfiguration("Parcourir ..."))
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class, //précise de quel type sera chaque entrée
                'allow_add' => true, //précise si on a le droit d'add de nouveaux éléments
                'allow_delete' => true, //permet de supprimer des élements liés
                'label' => false
            ])
            ->add('rooms', IntegerType::class, $this->getConfiguration("Nombre de chambres"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
            'translation_domain' => 'forms'
        ]);
    }
}

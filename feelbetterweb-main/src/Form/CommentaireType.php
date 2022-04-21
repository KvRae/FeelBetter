<?php


namespace App\Form;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Entity\Patient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu',TextAreaType::class,[

        'label'=>'contenu',
        'required'=>false,])
           // ->add('date',DateType::class,['widget'=>'single_text','required'=>false,])
            ->add('idpatient' ,EntityType::class,['class'=>Patient::class,'choice_label'=>'nom',
                'required'=>'false','label'=>'patient'
            ])
            ->add('idarticle' ,EntityType::class,['class'=>Article::class,'choice_label'=>'titre',
                'required'=>'false','label'=>'article'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}

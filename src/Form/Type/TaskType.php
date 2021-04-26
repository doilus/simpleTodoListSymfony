<?php


namespace App\Form\Type;


use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['attr' => ['type' => '']])
            ->add('slug')
            ->add('description')
            ->add('dueDate', DateType::class)
            ->add('isDone')
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,     //not set html5 validation
                'constraints' => [
                    new Image([             //validation - only image types
                        'maxSize' => '5k'   //set size with parametres - 5 kB
                    ])
                ]
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ],
                'label' => 'Update task'
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
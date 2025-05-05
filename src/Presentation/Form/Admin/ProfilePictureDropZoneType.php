<?php

declare(strict_types=1);

namespace App\Presentation\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\Dropzone\Form\DropzoneType;

class ProfilePictureDropZoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('profilePicture', DropzoneType::class, [
                'label' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Drag and drop a file here or click to select a file',
                    'accept' => 'image/jpeg,image/png, image/jpg',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'mt-2 btn btn-primary',
                ],
            ])
        ;
    }
}

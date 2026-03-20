<?php

namespace App\Form;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class BulkBankMovementsFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('fichero', FileType::class, [
                'label' => false,
                'required' => true
            ])
        ;
    }

}

<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\CategorizarMovimientoTypeForm;

class CategorizarMovimientosTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('movimientos', CollectionType::class, [
            'entry_type' => CategorizarMovimientoTypeForm::class,
            'label' => false,
        ]);
    }
}

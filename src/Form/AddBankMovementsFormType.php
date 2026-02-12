<?php

namespace App\Form;
 
use App\Entity\MovimientoBancario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class AddBankMovementsFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('cantidad', MoneyType::class, [
                'required' => true
            ])
            ->add('concepto', TextareaType::class, [
                'required' => true
            ])
            ->add('fecha', DateTimeType::class, [
                'required' => false,
                'format' => 'dd-MM-yyyy hh:mm:ss',
                'html5' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MovimientoBancario::class,
        ]);
    }
}

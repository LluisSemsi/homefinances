<?php

namespace App\Form;

use App\Entity\EstadoFondoInversion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class AnadirEstadoFondoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fecha', DateType::class, [
                'widget' => 'single_text',
                'label'  => false,
                'data'   => new \DateTime(),
                'constraints' => [
                    new NotBlank(['message' => 'La fecha es obligatoria.']),
                ],
            ])
            ->add('periodicidad', TextType::class, [
                'label'  => false,
                'constraints' => [
                    new NotBlank(['message' => 'La periodicidad es obligatoria.']),
                ],
            ])
            ->add('duracion', TextType::class, [
                'label'  => false,
                'constraints' => [
                    new NotBlank(['message' => 'La duración es obligatoria.']),
                ],
            ])
            ->add('numParticipaciones', NumberType::class, [
                'label'  => false,
                'scale'  => 4,
                'constraints' => [
                    new NotBlank(['message' => 'El número de participaciones es obligatorio.']),
                    new Positive(['message' => 'Las participaciones deben ser positivas.']),
                ],
            ])
            ->add('precioMedio', NumberType::class, [
                'label'  => false,
                'scale'  => 2,
                'constraints' => [
                    new NotBlank(['message' => 'El precio medio es obligatorio.']),
                    new Positive(['message' => 'El precio medio debe ser positivo.']),
                ],
            ])
            ->add('ultimoPrecio', NumberType::class, [
                'label'  => false,
                'scale'  => 2,
                'constraints' => [
                    new NotBlank(['message' => 'El último precio es obligatorio.']),
                    new Positive(['message' => 'El último precio debe ser positivo.']),
                ],
            ])
            ->add('importeInvertido', NumberType::class, [
                'label'  => false,
                'scale'  => 2,
                'constraints' => [
                    new NotBlank(['message' => 'El importe invertido es obligatorio.']),
                    new Positive(['message' => 'El importe invertido debe ser positivo.']),
                ],
            ])
            ->add('valorActual', NumberType::class, [
                'label'  => false,
                'scale'  => 2,
                'constraints' => [
                    new NotBlank(['message' => 'El valor actual es obligatorio.']),
                    new Positive(['message' => 'El valor actual debe ser positivo.']),
                ],
            ])
            ->add('dividendos', NumberType::class, [
                'label'    => false,
                'scale'    => 2,
                'required' => false,
                'empty_data' => '0',
                'constraints' => [
                    new PositiveOrZero(['message' => 'Los dividendos no pueden ser negativos.']),
                ],
            ])
            ->add('diferencia', NumberType::class, [
                'label'  => false,
                'scale'  => 2,
                'constraints' => [
                    new NotBlank(['message' => 'La diferencia de periodo es obligatoria.']),
                ],
            ])
            ->add('diferenciaTotal', NumberType::class, [
                'label'  => false,
                'scale'  => 2,
                'constraints' => [
                    new NotBlank(['message' => 'La diferencia total es obligatoria.']),
                ],
            ])
            ->add('diferenciaTotalPercent', NumberType::class, [
                'label'  => false,
                'scale'  => 4,
                'constraints' => [
                    new NotBlank(['message' => 'El porcentaje de rentabilidad es obligatorio.']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EstadoFondoInversion::class,
        ]);
    }
}
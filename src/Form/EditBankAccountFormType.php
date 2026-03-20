<?php

namespace App\Form;
 
use App\Entity\CuentaBancaria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class EditBankAccountFormType extends AbstractType
{

    private UserRepository $userRepository;
    private Security $security;

    public function __construct(UserRepository $userRepository, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $users = $this->userRepository->findAll();

        $choises = [];

        /** @var \App\Entity\User $usuario_actual */
        $usuario_actual = $this->security->getUser();

        
        foreach($users as $user){
            if($user->getId() != $usuario_actual->getId()){
                $choises[$user->getUsername()] = $user->getId();
            }            
        }

        $builder
            ->add('nombre_banco', TextType::class, [
                'required' => true
            ])
            ->add('titular', TextType::class, [
                'required' => true
            ])
            ->add('alias', TextType::class, [
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CuentaBancaria::class,
        ]);
    }
}

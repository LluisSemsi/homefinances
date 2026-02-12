<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\CuentaBancaria;
use App\Entity\User;
use App\Entity\MovimientoBancario;
use App\Form\CreateAccountFormType;
use App\Form\AddBankMovementsFormType;
use App\Form\EditBankAccountFormType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\CuentaBancariaRepository;
use App\Repository\MovimientoBancarioRepository;

class PanelController extends AbstractController
{
    
    public function home(): Response
    {

        if (!$this->getUser()) {            
            return $this->redirectToRoute('home',);
        }

        /** @var \App\Entity\User $user */ /* LE DECIMOS A SYMFONY QUE $user ES DE TIPO User(Entity) */
        $user = $this->getUser();

        $num_cuentas_usuario = count($user->getCuentasBancarias());

        return $this->render('panel/index.html.twig', [
            'user' => $user,
            'title' => 'Tu Panel',
            'num_cuentas_usuario' => $num_cuentas_usuario,
            'cuentas_bancarias' => $user->getCuentasBancarias()
        ]);
    }

    public function crear_cuentas_bancarias(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {

        if (!$this->getUser()) {            
            return $this->redirectToRoute('home',);
        }

        /** @var \App\Entity\User $user */ /* LE DECIMOS A SYMFONY QUE $user ES DE TIPO User(Entity) */
        $user = $this->getUser();

        $cuentaBancaria = new CuentaBancaria();
        $form = $this->createForm(CreateAccountFormType::class);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $iban = $form->get('iban')->getData();
            $nombre_banco = $form->get('nombre_banco')->getData();
            $titular = $form->get('titular')->getData();
            $user_id = $form->get('usuario_asociado')->getData();

            $usuario_asociado = $userRepository->find($user_id);

            $cuentaBancaria->setIBAN($iban);
            $cuentaBancaria->setNombreBanco($nombre_banco);
            $cuentaBancaria->setTitular($titular);
            $cuentaBancaria->setSaldo(0);
            $cuentaBancaria->addUsuario($user);
            $cuentaBancaria->addUsuario($usuario_asociado);

        
            // encode the plain password

            $entityManager->persist($cuentaBancaria);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $this->redirectToRoute('panel_home');
        }

        return $this->render('panel/create_cuentas_bancarias.html.twig', [
            'newAccountForm' => $form,
            'user' => $user,
            'title' => 'Crear Cuentas'
        ]);
    }

    public function add_movimientos(Request $request, EntityManagerInterface $entityManager, CuentaBancariaRepository $cuentaBancariaRepository, MovimientoBancarioRepository $movimientoBancarioRepository): Response

    {
        $iban = $request->attributes->get('iban');

        /** @var \App\Entity\CuentaBancaria $cuentaBancaria */
        $cuentaBancaria = $cuentaBancariaRepository->find($iban);

        if(is_null($cuentaBancaria))
            return $this->redirectToRoute('error', ['code' => 'CUENTA_NOT_FOUND']);

        if (!$this->getUser())         
            return $this->redirectToRoute('home',);

        /** @var \App\Entity\User $user */  /* LE DECIMOS A SYMFONY QUE $user ES DE TIPO User(Entity) */
        $user = $this->getUser();

        $form = $this->createForm(AddBankMovementsFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $movimientoCuenta = new MovimientoBancario();
            $movimientoCuenta->setCuenta($cuentaBancaria);
            $movimientoCuenta->setFecha($form->get('fecha')->getData());
            $movimientoCuenta->setConcepto($form->get('concepto')->getData());
            $movimientoCuenta->setCantidad($form->get('cantidad')->getData());

            $entityManager->persist($movimientoCuenta);
            $entityManager->flush();

            $saldo_actual = $cuentaBancaria->getSaldo() + $form->get('cantidad')->getData();

            $cuentaBancaria->setSaldo($saldo_actual);
            $entityManager->persist($cuentaBancaria);
            $entityManager->flush();

            return $this->redirectToRoute('panel_home');

        }


        return $this->render('panel/add_movement.html.twig', [
            'title' => 'Nuevo movimiento',
            'user' => $user,
            'add_movement_form' => $form,
            'nombre_banco' => $cuentaBancaria->getNombreBanco()
        ]);   
    }

    public function editar_cuenta(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, CuentaBancariaRepository $cuentaBancariaRepository): Response

    {
        $iban = $request->attributes->get('iban');
        $cuentaBancaria = $cuentaBancariaRepository->find($iban);

        if(is_null($cuentaBancaria))
            return $this->redirectToRoute('error', ['code' => 'CUENTA_NOT_FOUND']);

        if (!$this->getUser())         
            return $this->redirectToRoute('home',);

        /** @var \App\Entity\User $user */ /* LE DECIMOS A SYMFONY QUE $user ES DE TIPO User(Entity) */
        $user = $this->getUser();

        $form = $this->createForm(EditBankAccountFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nombre_banco = $form->get('nombre_banco')->getData();
            $titular = $form->get('titular')->getData();

            $cuentaBancaria->setNombreBanco($nombre_banco);
            $cuentaBancaria->setTitular($titular);

            $entityManager->persist($cuentaBancaria);
            $entityManager->flush();

            return $this->redirectToRoute('panel_home');

        }

        return $this->render('panel/edit_cuentas_bancarias.html.twig', [
            'title' => 'Editar Cuenta',
            'EditAccountForm' => $form,
            'user' => $user,
            'iban' => $iban
        ]);   
    }

    public function eliminar_cuenta(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, CuentaBancariaRepository $cuentaBancariaRepository): Response

    {
        $iban = $request->attributes->get('iban');
        $cuentaBancaria = $cuentaBancariaRepository->find($iban);

        if(is_null($cuentaBancaria))
            return $this->redirectToRoute('error', ['code' => 'CUENTA_NOT_FOUND']);

        if (!$this->getUser())         
            return $this->redirectToRoute('home',);

        /** @var \App\Entity\User $user */ /* LE DECIMOS A SYMFONY QUE $user ES DE TIPO User(Entity) */
        $user = $this->getUser();

        return $this->render('panel/delete_cuentas_bancarias.html.twig', [
            'title' => 'Eliminar Cuenta',
            'user' => $user,
            'cuenta_bancaria' => $cuentaBancaria

        ]);   
    }

    public function confirmar_eliminar_cuenta(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, CuentaBancariaRepository $cuentaBancariaRepository): Response

    {
        $iban = $request->attributes->get('iban');
        $cuentaBancaria = $cuentaBancariaRepository->find($iban);

        if(is_null($cuentaBancaria))
            return $this->redirectToRoute('error', ['code' => 'CUENTA_NOT_FOUND']);

        if (!$this->getUser())         
            return $this->redirectToRoute('home',);

        $entityManager->remove($cuentaBancaria);
        $entityManager->flush();

        return $this->redirectToRoute('panel_home');   
    }

}

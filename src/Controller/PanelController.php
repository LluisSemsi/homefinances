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
use App\Form\BulkBankMovementsFormType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\CuentaBancariaRepository;
use App\Repository\MovimientoBancarioRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Import\MovimientoBancarioImporter;
use App\Import\Parser\BancoMediolanumParser;
use App\Import\Parser\BancoCaixaRuralParser;
use App\Import\Parser\BancoSabadellParser;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\CategorizarMovimientosTypeForm;
use App\Repository\TipoMovimientoBancarioRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ProductoInversionRepository;
use App\Repository\MovimientoInversionRepository;
use App\Repository\FondoInversionRepository;
use App\Entity\EstadoFondoInversion;
use App\Form\AnadirEstadoFondoType;

class PanelController extends AbstractController
{
    
    public function home(EntityManagerInterface $entityManage, MovimientoBancarioRepository $movimientoBancarioRepository): Response
    {

        if (!$this->getUser()) {            
            return $this->redirectToRoute('home',);
        }

        /** @var \App\Entity\User $user */ /* LE DECIMOS A SYMFONY QUE $user ES DE TIPO User(Entity) */
        $user = $this->getUser();

        $cuentas_bancarias = $user->getCuentasBancarias();
        $num_cuentas_usuario = count($cuentas_bancarias);

        $saldo_total_cuentas = 0;
        foreach($cuentas_bancarias as $cuenta){
            $saldo_total_cuentas += $cuenta->getSaldo();
        }

        $fechaInicio = new \DateTimeImmutable('first day of this month 00:00:00');
        $fechaFin    = new \DateTimeImmutable('last day of this month 23:59:59');

        $balance_mensual = $movimientoBancarioRepository->obtenerBalancePeriodoUsuario($fechaInicio, $fechaFin, $user->getId());
        $ingresos_mensual = $movimientoBancarioRepository->obtenerIngresosTotalesPeriodoUsuario($fechaInicio, $fechaFin, $user->getId());


        return $this->render('panel/index.html.twig', [
            'user' => $user,
            'title' => 'Tu panel ' . $user->getUsername(),
            'saldo_total_cuentas' => $saldo_total_cuentas,
            'num_cuentas_usuario' => $num_cuentas_usuario,
            'ingresos_mes' => $ingresos_mensual,
            'balance_mes' => $balance_mensual,
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
            $alias = $form->get('alias')->getData();
            $user_id = $form->get('usuario_asociado')->getData();

            $usuario_asociado = $userRepository->find($user_id);

            $cuentaBancaria->setIBAN($iban);
            $cuentaBancaria->setNombreBanco($nombre_banco);
            $cuentaBancaria->setTitular($titular);
            $cuentaBancaria->setAlias($alias);
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

    public function detalles_cuenta_bancaria(Request $request, PaginatorInterface $paginator, 
    CuentaBancariaRepository $cuentaBancariaRepository, MovimientoBancarioRepository $movimientoBancarioRepository,
    TipoMovimientoBancarioRepository $tipoMovimientoBancarioRepository): Response

    {
        $iban = $request->attributes->get('iban');

        $cuentaBancaria = $cuentaBancariaRepository->findOneBy(['iban'=>$iban]);

        if(is_null($cuentaBancaria))
            return $this->redirectToRoute('error', ['code' => 'CUENTA_NOT_FOUND']);

        if (!$this->getUser())         
            return $this->redirectToRoute('home',);

        /** @var \App\Entity\User $user */ 
        $user = $this->getUser();

        /* Obtener datos para los gráficos de sumario */

        $ingresosGastos = $movimientoBancarioRepository->getIngresosGastosUltimosXMes($cuentaBancaria, 6);
        $gastosCategorias = $movimientoBancarioRepository->getGastosPorCategoriaYMeses($cuentaBancaria, 1);


        /* Obtener el filtro elegido y buscar movimientos en la base de datos */

        $filtro = $request->query->get('filtro', null); // 'mes', 'trimestre', 'semestre', 'anio'
        $fechaDesde = $request->query->get('desde', null);
        $fechaHasta = $request->query->get('hasta', null);

        // Calcular fechas según filtro predefinido
        $desde = null;
        $hasta = new \DateTime('today');

        if ($filtro) {
            $desde = new \DateTime('today');
            match($filtro) {
                'mes'       => $desde->modify('-1 month'),
                'trimestre' => $desde->modify('-3 months'),
                'semestre'  => $desde->modify('-6 months'),
                'anio'      => $desde->modify('-1 year'),
            };
        } elseif ($fechaDesde && $fechaHasta) {
            $desde = \DateTime::createFromFormat('Y-m-d', $fechaDesde);
            $hasta = \DateTime::createFromFormat('Y-m-d', $fechaHasta);
        }

        $movimientos = $movimientoBancarioRepository->getMovimientosCuenta($paginator, $cuentaBancaria, $request, $desde, $hasta);
       
        $tipos = $tipoMovimientoBancarioRepository->findAll();

        return $this->render('panel/detalles_cuenta_bancaria.html.twig', [
            'title' => 'Detalles Cuenta',
            'user' => $user,
            'cuenta' => $cuentaBancaria,
            'movimientos' => $movimientos,
            'num_movimientos' => count($movimientos->getItems()),
            'filtro_activo' => $filtro,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
            'ingresos_gastos' => $ingresosGastos,
            'gastos_categorias' => $gastosCategorias,
            'tipos' => $tipos
        ]);   
    }

    public function add_movimientos(Request $request, EntityManagerInterface $entityManager, CuentaBancariaRepository $cuentaBancariaRepository, MovimientoBancarioRepository $movimientoBancarioRepository): Response

    {
        $iban = $request->attributes->get('iban');

        /** @var \App\Entity\CuentaBancaria $cuentaBancaria */
        $cuentaBancaria = $cuentaBancariaRepository->findOneBy(['iban'=>$iban]);

        if(is_null($cuentaBancaria))
            return $this->redirectToRoute('error', ['code' => 'CUENTA_NOT_FOUND']);

        if (!$this->getUser())         
            return $this->redirectToRoute('home',);

        /** @var \App\Entity\User $user */  /* LE DECIMOS A SYMFONY QUE $user ES DE TIPO User(Entity) */
        $user = $this->getUser();

        $form = $this->createForm(AddBankMovementsFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $saldo_actual = $cuentaBancaria->getSaldo() + $form->get('cantidad')->getData();
            
            $movimientoCuenta = new MovimientoBancario();
            $movimientoCuenta->setCuenta($cuentaBancaria);
            $movimientoCuenta->setFecha($form->get('fecha')->getData());
            $movimientoCuenta->setConcepto($form->get('concepto')->getData());
            $movimientoCuenta->setCantidad($form->get('cantidad')->getData());
            $movimientoCuenta->setSaldoActual($saldo_actual);

            $entityManager->persist($movimientoCuenta);
            $entityManager->flush();
            

            $cuentaBancaria->setSaldo($saldo_actual);
            $entityManager->persist($cuentaBancaria);
            $entityManager->flush();

            return $this->redirectToRoute('panel_home');

        }


        return $this->render('panel/add_movement.html.twig', [
            'title' => 'Nuevo movimiento',
            'user' => $user,
            'cuenta' => $cuentaBancaria,
            'add_movement_form' => $form,
            'nombre_banco' => $cuentaBancaria->getNombreBanco()
        ]);   
    }

    public function editar_cuenta(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, CuentaBancariaRepository $cuentaBancariaRepository): Response

    {
        $iban = $request->attributes->get('iban');
        $cuentaBancaria = $cuentaBancariaRepository->findOneBy(['iban'=>$iban]);

        if(is_null($cuentaBancaria))
            return $this->redirectToRoute('error', ['code' => 'CUENTA_NOT_FOUND']);

        if (!$this->getUser())         
            return $this->redirectToRoute('home',);

        /** @var \App\Entity\User $user */ /* LE DECIMOS A SYMFONY QUE $user ES DE TIPO User(Entity) */
        $user = $this->getUser();

        $form = $this->createForm(EditBankAccountFormType::class, $cuentaBancaria);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nombre_banco = $form->get('nombre_banco')->getData();
            $titular = $form->get('titular')->getData();
            $alias = $form->get('alias')->getData();

            $cuentaBancaria->setNombreBanco($nombre_banco);
            $cuentaBancaria->setTitular($titular);
            $cuentaBancaria->setAlias($alias);

            $entityManager->persist($cuentaBancaria);
            $entityManager->flush();

            return $this->redirectToRoute('panel_home');

        }

        return $this->render('panel/edit_cuentas_bancarias.html.twig', [
            'title' => 'Editar Cuenta',
            'EditAccountForm' => $form,
            'user' => $user,
            'iban' => $iban,
            'cuenta' => $cuentaBancaria
        ]);   
    }

    public function eliminar_cuenta(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, CuentaBancariaRepository $cuentaBancariaRepository): Response

    {
        $iban = $request->attributes->get('iban');
        $cuentaBancaria = $cuentaBancariaRepository->findOneBy(['iban'=>$iban]);;

        if(is_null($cuentaBancaria))
            return $this->redirectToRoute('error', ['code' => 'CUENTA_NOT_FOUND']);

        if (!$this->getUser())         
            return $this->redirectToRoute('home',);

        /** @var \App\Entity\User $user */ /* LE DECIMOS A SYMFONY QUE $user ES DE TIPO User(Entity) */
        $user = $this->getUser();

        return $this->render('panel/delete_cuentas_bancarias.html.twig', [
            'title' => 'Eliminar Cuenta',
            'user' => $user,
            'cuenta' => $cuentaBancaria

        ]);   
    }

    public function confirmar_eliminar_cuenta(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, CuentaBancariaRepository $cuentaBancariaRepository): Response

    {
        $iban = $request->attributes->get('iban');
        $cuentaBancaria = $cuentaBancariaRepository->findOneBy(['iban'=>$iban]);

        if(is_null($cuentaBancaria))
            return $this->redirectToRoute('error', ['code' => 'CUENTA_NOT_FOUND']);

        if (!$this->getUser())         
            return $this->redirectToRoute('home',);

        $entityManager->remove($cuentaBancaria);
        $entityManager->flush();

        return $this->redirectToRoute('panel_home');   
    }

    public function bulk_movimientos_bancarios(Request $request, EntityManagerInterface $entityManager, 
    CuentaBancariaRepository $cuentaBancariaRepository,MovimientoBancarioImporter $importer, 
    BancoMediolanumParser $mediolanumParser, BancoCaixaRuralParser $caixaruralParser, BancoSabadellParser $bancosabadellParser): Response
    {

        $iban = $request->attributes->get('iban');
        $cuentaBancaria = $cuentaBancariaRepository->findOneBy(['iban'=>$iban]);

        if(is_null($cuentaBancaria))
            return $this->redirectToRoute('error', ['code' => 'CUENTA_NOT_FOUND']);

        if (!$this->getUser())         
            return $this->redirectToRoute('home',);

        /** @var \App\Entity\User $user */ /* LE DECIMOS A SYMFONY QUE $user ES DE TIPO User(Entity) */
        $user = $this->getUser();

        $form = $this->createForm(BulkBankMovementsFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $nombreBanco = strtolower($cuentaBancaria->getNombreBanco());

            switch ($nombreBanco) {
                case 'banco mediolanum':
                    $parser = $mediolanumParser;
                    break;

                case 'grupo cooperativo cajamar':
                    $parser = $caixaruralParser;
                    break;
                
                case 'banco sabadell':
                    $parser = $bancosabadellParser;
                    break;

                default:
                    throw new \RuntimeException("No hay parser definido para el banco: $nombreBanco");

            }

            /** @var UploadedFile $file */
            $file = $form->get('fichero')->getData();

            if (!$file) {
                $this->addFlash('error', 'No se ha subido ningún fichero.');
                return $this->redirectToRoute('bulk_movimientos_bancarios', ['iban' => $iban]);
            }

            // Guardar fichero temporalmente
            $tempPath = sys_get_temp_dir() . '/' . uniqid('movs_', true) . '.' . $file->guessExtension();
            $file->move(dirname($tempPath), basename($tempPath));

            // Importar movimientos (sin detector)
            $numImportados = $importer->importFromParser(
                $tempPath,
                $cuentaBancaria,
                $parser
            );

            $this->addFlash('success', "Se han importado $numImportados movimientos.");

            return $this->redirectToRoute('panel_home');

        }

        return $this->render('panel/bulk_movimientos_bancarias.html.twig', [
            'title' => 'Subir Movimientos Bancarios',
            'BulkMovementsForm' => $form,
            'user' => $user,
            'cuenta' => $cuentaBancaria
        ]);

    }
    
    public function categorizar_movimientos_bancarios(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator, 
    CuentaBancariaRepository $cuentaBancariaRepository, MovimientoBancarioRepository $movimientoBancarioRepository): Response 
    {
        $iban = $request->attributes->get('iban');
        $cuentaBancaria = $cuentaBancariaRepository->findOneBy(['iban'=>$iban]);

        if(is_null($cuentaBancaria))
            return $this->redirectToRoute('error', ['code' => 'CUENTA_NOT_FOUND']);

        if (!$this->getUser())         
            return $this->redirectToRoute('home',);

        /** @var \App\Entity\User $user */ /* LE DECIMOS A SYMFONY QUE $user ES DE TIPO User(Entity) */
        $user = $this->getUser();

        $movimientos  = $movimientoBancarioRepository->getMovimientosBancariosSinCategorizar($paginator, $cuentaBancaria, $request);

        // Construyes el formulario con los movimientos de la página actual
        $data = ['movimientos' => iterator_to_array($movimientos)];

        $form = $this->createForm(CategorizarMovimientosTypeForm::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('categorizar-movimientos-bancarios', ['iban' => $iban]);
        }

        return $this->render('panel/categorizar_movimientos_bancario.html.twig', [
            'form' => $form,
            'movimientos' => $movimientos,
            'cuenta' => $cuentaBancaria,
            'user' => $user,
            'num_movimientos' => count($movimientos->getItems())
        ]);
    }

    public function editarMovimiento(int $id, Request $request, EntityManagerInterface $em, MovimientoBancarioRepository $movimientoBancarioRepository,
        TipoMovimientoBancarioRepository $tipoMovimientoBancarioRepository): JsonResponse 
    {
        $movimiento = $movimientoBancarioRepository->find($id);

        if (!$movimiento) {
            return $this->json(['error' => 'Movimiento no encontrado'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $movimiento->setConcepto($data['concepto']);

        $tipo = $tipoMovimientoBancarioRepository->find($data['tipo_id']);
        if (!$tipo) {
            return $this->json(['error' => 'Categoría no encontrada'], 404);
        }

        $movimiento->setTipo($tipo);
        $em->flush();

        return $this->json(['success' => true]);
    }

    public function inversiones(Request $request, EntityManagerInterface $em, ProductoInversionRepository $productoInversionRepository,
        MovimientoInversionRepository $movimientoInversionRepository): Response 
    {
        if (!$this->getUser()) {            
            return $this->redirectToRoute('home',);
        }

        /** @var \App\Entity\User $user */ /* LE DECIMOS A SYMFONY QUE $user ES DE TIPO User(Entity) */
        $user = $this->getUser();

        // Productos del usuario con sus fondos
        $productos = $productoInversionRepository->getProductosConFondos($user);


        // Capital invertido total
        $capitalInvertido = $movimientoInversionRepository->getTotalInvertido($user);

        // Valor actual total (suma del último estado de cada fondo)
        $valorActual = $productoInversionRepository->getValorActualTotal($user);

        // Rentabilidad
        $rentabilidadAbsoluta = $valorActual - $capitalInvertido;
        $rentabilidadPercent = $capitalInvertido > 0 
            ? ($rentabilidadAbsoluta / $capitalInvertido) * 100 
            : 0;

        // Aportado este año
        $aportadoEsteAnio = $movimientoInversionRepository->getTotalInvertidoAnio($user, (int) date('Y'));

        // Últimas 5 aportaciones
        $ultimasAportaciones = $movimientoInversionRepository->getUltimasAportaciones($user, 5);

        // Datos para el gráfico de evolución
        $datosGrafico = $productoInversionRepository->getEvolucionPortfolio($user);


        return $this->render('panel/inversiones.html.twig', [
            'user' => $user,
            'productos' => $productos,
            'capital_invertido' => $capitalInvertido,
            'valor_actual' => $valorActual,
            'rentabilidad_absoluta' => $rentabilidadAbsoluta,
            'rentabilidad_percent' => $rentabilidadPercent,
            'aportado_anio' => $aportadoEsteAnio,
            'ultimas_aportaciones' => $ultimasAportaciones,
            'datos_grafico' => $datosGrafico,
        ]); 
    }


    public function productoInversion(int $id, Request $request, ProductoInversionRepository $productoInversionRepository): Response 
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('home');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $producto = $productoInversionRepository->find($id);

        if (!$producto) {
            return $this->redirectToRoute('error', ['code' => 'UNKNOWN_ERROR']);
        }

        return $this->render('panel/producto_inversion.html.twig', [
            'user' => $user,
            'producto' => $producto,
            'title' => $producto->getNombre(),
        ]);
    }

    public function anadirEstadoFondo(int $id, Request $request, FondoInversionRepository $fondoInversionRepository,
        EntityManagerInterface $em
    ): Response {
 
        if (!$this->getUser()) {
            return $this->redirectToRoute('home');
        }
 
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
 
        $fondo = $fondoInversionRepository->find($id);
 
        // Seguridad: el fondo debe existir y pertenecer al usuario
        if (!$fondo || !$fondo->getProductoInversion()->getCuentaBancaria()->getUsuarios()->contains($user)) {
            return $this->redirectToRoute('inversiones');
        }
 
        $estado = new EstadoFondoInversion();
        $form   = $this->createForm(AnadirEstadoFondoType::class, $estado);
        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
            $estado->setFondoInversion($fondo);
            $em->persist($estado);
            $em->flush();
 
            return $this->redirectToRoute('producto-inversion', [
                'id' => $fondo->getProductoInversion()->getId(),
            ]);
        }
 
        return $this->render('panel/anadir_estado_fondo.html.twig', [
            'form'  => $form,
            'fondo' => $fondo,
            'user'  => $user,
        ]);
    }

}


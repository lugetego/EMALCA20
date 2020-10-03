<?php

namespace App\Controller;

use App\Entity\Registro;
use App\Form\RegistroType;
use App\Repository\RegistroRepository;
use Metadata\Tests\Driver\Fixture\A\A;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;



/**
 * @Route("/registro")
 */
class RegistroController extends AbstractController
{
    /**
     * @Route("/", name="registro_index", methods={"GET"})
     */
    public function index(RegistroRepository $registroRepository): Response
    {
        return $this->render('registro/index.html.twig', [
            'registros' => $registroRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="registro_new", methods={"GET","POST"})
     */
    public function new(Request $request, \Swift_Mailer $mailer): Response
    {
        $registro = new Registro();
        $form = $this->createForm(RegistroType::class, $registro);
        $form->remove('referencia');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($registro);
            $entityManager->flush();


            // Correo electrónico alumno
            $message = (new \Swift_Message('EMALCA 2020 - Registro'))
                ->setFrom('webmaster@matmor.unam.mx')
                ->setTo(array($registro->getCorreo() ))
//            ->setTo('gerardo@matmor.unam.mx')
                ->setBcc(array('gerardo@matmor.unam.mx'))
                ->setBody($this->renderView('registro/confirmacion.txt.twig', 
                    array('registro' => $registro)));

            ;
            $mailer->send($message);

            // Correo electrónico profesor
            $message = (new \Swift_Message('EMALCA 2020 - Recomendación'))
                ->setFrom('webmaster@matmor.unam.mx')
                ->setTo(array($registro->getprofesorCorreo() ))
//            ->setTo('gerardo@matmor.unam.mx')
                ->setBcc(array('gerardo@matmor.unam.mx'))
                ->setBody($this->renderView('registro/referencia.txt.twig',
                    array('registro' => $registro)));

            ;
            $mailer->send($message);

            return $this->render('registro/confirmacion.html.twig');
        }

        return $this->render('registro/new.html.twig', [
            'registro' => $registro,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="registro_show", methods={"GET"})
     */
    public function show(Registro $registro): Response
    {
        return $this->render('registro/show.html.twig', [
            'registro' => $registro,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="registro_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Registro $registro): Response
    {
        $form = $this->createForm(RegistroType::class, $registro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('registro_index');
        }

        return $this->render('registro/edit.html.twig', [
            'registro' => $registro,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/{correo}/ref", name="registro_ref",  methods={"GET","POST"})
     * @ParamConverter("registro", options={"exclude": {"correo"}})
     */
    public function ref(Request $request, Registro $registro, $slug, $correo, \Swift_Mailer $mailer): Response
    {
        /*$registro = $this->getDoctrine()
            ->getRepository(Registro::class)
            ->findOneBy(['slug'=> $slug]);*/



        if( $registro->getCorreo() == $registro->getprofesorCorreo() ||  $correo != $registro->getCorreo() || $slug != $registro->getSlug()){

            throw $this->createNotFoundException('Existe algún problema con la información de registro favor de contactar a webmaster@matmor.unam.mx'.
                $registro->getCorreo()."=".$correo.'s'.$registro->getprofesorCorreo().'\bn'.".".$slug.".".$registro->getSlug()
            );
        }

      /*  if( $registro->getCartaName() != null || $registro->getRecomendacion() != null)
        {
            return $this->render('form/confirmCarta.html.twig', array('id' => $registro->getId(),'entity'=>$registro));
        }*/

        $form = $this->createForm(RegistroType::class, $registro);
        $form->remove('nombre');
        $form->remove('apaterno');
        $form->remove('genero');
        $form->remove('correo');
        $form->remove('pais');
        $form->remove('institucion');
        $form->remove('profesorInst');
        $form->remove('porcentaje');
        $form->remove('profesorCorreo');
        $form->remove('historialFile');
        $form->remove('credencialFile');
        $form->remove('motivos');

        $tmp = $registro->getPorcentaje();
        $registro->setPorcentaje('0');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $registro->setPorcentaje($tmp);
            $registro->setModifiedAt(new \DateTime());
            $em->persist($registro);

            $this->getDoctrine()->getManager()->flush();

            // Correos electrónico
            $message = (new \Swift_Message('EMALCA 2020 - Recomendación recibida'))
                ->setFrom('webmaster@matmor.unam.mx')
                ->setTo(array($registro->getprofesorCorreo() ))
                ->setCc($registro->getCorreo())
//            ->setTo('gerardo@matmor.unam.mx')
                ->setBcc(array('gerardo@matmor.unam.mx'))
                ->setBody($this->renderView('registro/confirmacionRef.txt.twig',
                    array('registro' => $registro)));

            ;
            $mailer->send($message);


            return $this->render('registro/confirmacionRef.html.twig',
                array('id' => $registro->getId(), 'entity' => $registro));
        }

        return $this->render('registro/referencia.html.twig', [
            'registro' => $registro,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="registro_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Registro $registro): Response
    {
        if ($this->isCsrfTokenValid('delete'.$registro->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($registro);
            $entityManager->flush();
        }

        return $this->redirectToRoute('registro_index');
    }
}

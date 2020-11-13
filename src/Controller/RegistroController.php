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
use Doctrine\Common\Collections\Criteria;




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

            throw $this->createNotFoundException('Existe algún problema con la información de registro favor de contactar a webmaster@matmor.unam.mx');
        }

        if( $registro->getReferencia() != null)
        {
            return $this->render('registro/confirmacionRef.html.twig',
                array('registro'=>$registro));
        }

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
            $registro->setSentAt(new \DateTime());
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
     * Displays a form to edit an existing Referencia entity.
     *
     * @Route("/{slug}/evaluacion", name="registro_eval",  methods={"GET","POST"})
     */

    public function eval(Request $request, Registro $registro, $slug): Response
    {

        /*  $em = $this->getDoctrine()->getManager();
          $entity = $em->getRepository('AppBundle:Registro')->find($registro);*/

        $formEval = $this->createFormBuilder($registro)

            ->add('aceptado','Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'  => array(
                    'Si' => true,
                    'No' => false,
                ),
                'expanded'=>true,
                'required'=>false,
                'placeholder'=>false,
            ))
            ->add('confirmado','Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                'choices'  => array(
                    'Si' => true,
                    'No' => false,
                ),
                'expanded'=>true,
                'required'=>false,
                'placeholder'=>false,
            ))
            ->add('comentarios', 'Symfony\Component\Form\Extension\Core\Type\TextareaType',  array(
                'label' => 'Comentarios',
                'required'=>false,
                'empty_data' => ''

            ))

            ->getForm();

        $tmp = $registro->getPorcentaje();
        $registro->setPorcentaje('0');

        $formEval->handleRequest($request);

        if ($formEval->isSubmitted() && $formEval->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $registro->setPorcentaje($tmp);
            $em->persist($registro);
            $em->flush();
            return $this->redirectToRoute('registro_show', array('slug' => $registro->getSlug()));

        }
        // $form   = $this->createForm($formEval, $entity);
        return $this->render('registro/eval.html.twig',
            array('registro' => $formEval->createView(),
                'slug'=> $registro->getSlug()));
        //return $this->redirect($this->generateUrl('registro_show', array('id' => $id)));

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


    /**
     * @Route("/export/aceptados", name="registro_aceptados",  methods={"GET","POST"})
     **/
    public function csvAceptados()
    {

        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository(Registro::class)->findBy(
            ['aceptado' => true],
            ['id' => 'ASC']

        );

        $rows = array();

        $data = array('Nombre','Apellido','Correo');
        $rows[] = implode(',', $data);

        foreach ($events as $event) {
            $data = array($event->getNombre(), $event->getApaterno(), $event->getCorreo());
            $rows[] = implode(',', $data);
        }

        $content = implode("\n", $rows);
        $response = new Response($content);

        $response->headers->set('Content-Type', 'text/csv');

        return $response;


    }

    /**
     * @Route("/export/noaceptados", name="registro_noaceptados",  methods={"GET","POST"})
     **/
    public function csvNoAceptados()
    {

        $em = $this->getDoctrine()->getManager();


        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('aceptado', false))
            ->orWhere(Criteria::expr()->eq('aceptado', null));

        $events = $em->getRepository(Registro::class)->matching($criteria);

        $rows = array();

        $data = array('Nombre','Apellido','Correo');
        $rows[] = implode(',', $data);

        foreach ($events as $event) {
            $data = array($event->getNombre(), $event->getApaterno(), $event->getCorreo());
            $rows[] = implode(',', $data);
        }

        $content = implode("\n", $rows);
        $response = new Response($content);


        $response->headers->set('Content-Type', 'text/csv ; charset=UTF-8');

        return $response;


    }
}

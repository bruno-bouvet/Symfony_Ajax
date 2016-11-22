<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Test;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilder;

/**
 * Test controller.
 *
 */
class TestController extends Controller
{
    /**
     * Lists all test entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tests = $em->getRepository('AppBundle:Test')->findAll();

        return $this->render('AppBundle:test:index.html.twig', array(
            'tests' => $tests,
        ));
    }

    public function ajaxAction()
    {
        $request = $this->container->get('request');
        $em = $this->getDoctrine()->getManager();
        $motcle = '';
        $motcle = $request->request->get('motcle');

        $em = $this->container->get('doctrine')->getEntityManager();

        if($motcle != '')
        {
            $qb = $em->createQueryBuilder();

            $qb->select('t')
                ->from('AppBundle:Test', 't')
                ->where("t.nom LIKE :motcle OR t.prenom LIKE :motcle")
                ->orderBy('t.nom', 'ASC')
                ->setParameter('motcle', $motcle.'%');

            $query = $qb->getQuery();
            $tests = $query->getResult();
        }
        else {
            $tests = $em->getRepository('AppBundle:Test')->findAll();
        }
        return $this->container->get('templating')->renderResponse('AppBundle:test:index.html.twig', array(
            'tests' => $tests,
        ));
    }

    /**
     * Creates a new test entity.
     *
     */
    public function newAction(Request $request)
    {
        $test = new Test();
        $form = $this->createForm('AppBundle\Form\TestType', $test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($test);
            $em->flush($test);

            return $this->redirectToRoute('test_show', array('id' => $test->getId()));
        }

        return $this->render('AppBundle:test:new.html.twig', array(
            'test' => $test,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a test entity.
     *
     */
    public function showAction(Test $test)
    {
        $deleteForm = $this->createDeleteForm($test);

        return $this->render('AppBundle:test:show.html.twig', array(
            'test' => $test,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing test entity.
     *
     */
    public function editAction(Request $request, Test $test)
    {
        $deleteForm = $this->createDeleteForm($test);
        $editForm = $this->createForm('AppBundle\Form\TestType', $test);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('test_edit', array('id' => $test->getId()));
        }

        return $this->render('AppBundle:test:edit.html.twig', array(
            'test' => $test,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a test entity.
     *
     */
    public function deleteAction(Request $request, Test $test)
    {
        $form = $this->createDeleteForm($test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($test);
            $em->flush($test);
        }

        return $this->redirectToRoute('test_index');
    }

    /**
     * Creates a form to delete a test entity.
     *
     * @param Test $test The test entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Test $test)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('test_delete', array('id' => $test->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

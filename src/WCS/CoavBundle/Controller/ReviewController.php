<?php
/**
 * Created by PhpStorm.
 * User: gilles
 * Date: 19/11/17
 * Time: 17:54
 */

namespace WCS\CoavBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

use WCS\CoavBundle\Entity\Review;
use WCS\CoavBundle\WCSCoavBundle;

/**
 * Class ReviewController
 * @Route("review")
 */
class ReviewController extends Controller
{
    /**
     * @Route("/", name="review_index")
     * @Method("GET")
     */

    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $reviews = $em->getRepository('WCSCoavBundle:Review')->findAll();

        return $this->render('review/index.html.twig', array(
            'reviews' => $reviews
        ));
    }

    /**
     * @Route("/new", name="review_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {

        $review = new Review();
        $form = $this->createForm('WCS\CoavBundle\Form\ReviewType', $review);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();

            // You can use too :
            // return $this->redirect($this->generateUrl('review_show', array('id' => $review->getId())))

            return $this->redirectToRoute('review_show', array('id' => $review->getId()));
        }

        return $this->render('review/new.html.twig', array(
            'review' => $review,
            'form' => $form->createView(),
        ));
    }


    /**
     * @Route("/{id}", name="review_show")
     * @Method("GET")
     */
    public function showAction(Review $review)
    {

        $form = $this->createDeleteForm($review);

        return $this->render('review/show.html.twig', array(
            'review' => $review,
            'deleteForm' => $form->createView()
        ));
    }

    /**
     * @Route("/{id}/edit", name="review_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Review $review)
    {
        $deleteForm = $this->createDeleteForm($review);
        $editForm = $this->createForm('WCS\CoavBundle\Form\ReviewType', $review);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('review_show', array('id' => $review->getId()));
        }

        return $this->render('review/edit.html.twig', array(
            'review' => $review,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     *
     *
     * @Route("/{id}", name="review_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Review $review)
    {
        $form = $this->createDeleteForm($review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($review);
            $em->flush();
        }

        return $this->redirectToRoute('review_index');
    }

    /**
     * @param Review $review
     * @return \Symfony\Component\Form\Form The form
     */
    public function createDeleteForm(Review $review)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('review_delete', array('id' => $review->getId())))
            ->setMethod('DELETE')
            ->getForm();


    }
}
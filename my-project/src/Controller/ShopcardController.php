<?php

namespace App\Controller;

use App\Entity\Shopcard;
use App\Form\ShopcardType;
use App\Repository\ShopcardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/shopcard")
 */
class ShopcardController extends AbstractController
{
    /**
     * @Route("/", name="shopcard_index", methods="GET")
     */
    public function index(ShopcardRepository $shopcardRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); //login kontrol güvenliği için

        $user=$this->getUser();

        $em=$this->getDoctrine()->getManager();
        $sql="SELECT p.title,p.saprice,p.image,p.description,s.*
              FROM shopcard s, product p
              WHERE s.productid=p.id AND userid= :userid";

        $statement=$em->getConnection()->prepare($sql);
        $statement->bindValue('userid',$user->getid());
        $statement->execute();
        $shopcards=$statement->fetchAll();

        return $this->render('shopcard/index.html.twig', ['shopcards' => $shopcards]);
    }

    /**
     * @Route("/new", name="shopcard_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $shopcard = new Shopcard();
        $form = $this->createForm(ShopcardType::class, $shopcard);
        $form->handleRequest($request);

        echo $submittedToken = $request->request->get('token');
        if($this->isCsrfTokenValid('add-item',$submittedToken))
        {
            if ($form->isSubmitted() ) {
                $em = $this->getDoctrine()->getManager();

                $user=$this->getUser();

                $shopcard->setUserid($user->getid());

                $em->persist($shopcard);
                $em->flush();

                return $this->redirectToRoute('shopcard_index');
            }
        }



        return $this->render('shopcard/new.html.twig', [
            'shopcard' => $shopcard,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="shopcard_show", methods="GET")
     */
    public function show(Shopcard $shopcard): Response
    {
        return $this->render('shopcard/show.html.twig', ['shopcard' => $shopcard]);
    }

    /**
     * @Route("/{id}/edit", name="shopcard_edit", methods="GET|POST")
     */
    public function edit(Request $request, Shopcard $shopcard): Response
    {
        $form = $this->createForm(ShopcardType::class, $shopcard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('shopcard_index', ['id' => $shopcard->getId()]);
        }

        return $this->render('shopcard/edit.html.twig', [
            'shopcard' => $shopcard,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/del", name="shopcard_del", methods="GET|POST")
     */
    public function del(Request $request, Shopcard $shopcard): Response
    {
        $em=$this->getDoctrine()->getManager();
        $em->remove($shopcard);
        $em->flush();
        $this->addFlash('success','Ürün Sepetten kaldırıldı');
        return $this->redirectToRoute('shopcard_index');
    }

    /**
     * @Route("/{id}", name="shopcard_delete", methods="DELETE")
     */
    public function delete(Request $request, Shopcard $shopcard): Response
    {
        if ($this->isCsrfTokenValid('delete'.$shopcard->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($shopcard);
            $em->flush();
        }

        return $this->redirectToRoute('shopcard_index');
    }
}

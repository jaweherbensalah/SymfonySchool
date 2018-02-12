<?php
/**
 * Created by PhpStorm.
 * User: digital
 * Date: 05/02/2018
 * Time: 13:50
 */

namespace AppBundle\Controller;

use AppBundle\File\FileUploader;
use AppBundle\Type\ShowType;
use AppBundle\Type\SearchType;
use AppBundle\Entity\Show;
use AppBundle\Entity\Categories;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route(name="show")
 */
class ShowController extends Controller
{
    /**
     * @Route("/show",name="_list")
     */
    public function listAction()
    {
        $repo = $this->getDoctrine()->getRepository(Show::class);
        $show = $repo->findAll();
        return $this->render('show/list.html.twig',['show' => $show]);
    }

    public function categoriesAction()
    {
        $repo = $this->getDoctrine()->getRepository(Categories::class);
        $categories = $repo->findAll();
        return $this->render('show/categories.html.twig',[
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/create",name="_create")
     */
    public function createAction(Request $request, FileUploader $fileUploader)
    {
        $show = new Show();
        $form = $this->createForm(ShowType::class, $show);

        $form->handleRequest($request);
        if($form->isValid()){

            $generatedFileName = $fileUploader->upload($show->getTmpimage(),$show->getCategories()->getName());

            $show->setImage($generatedFileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($show);
            $em->flush();

            $this->addFlash('success','Create success !');

            return $this->redirectToRoute('show_list');
        }
        return $this->render('show/create.html.twig',['showForm'=>$form->createView()]);
    }

    /**
     * @Route("/update/{id}", name="_update")
     */
    public function updateAction(Show $show, Request $request, FileUploader $fileUploader)
    {
        $showForm = $this->createForm(ShowType::class, $show, ['validation_groups'=> ['update']]);

        $showForm->handleRequest($request);

        if($showForm->isValid()){
            if($show->getTmpimage() != null){
                $generatedFileName = $fileUploader->upload($show->getTmpimage(),$show->getCategories()->getName());
                $show->setImage($generatedFileName);   
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($show);
            $em->flush();
            $this->addFlash('success', 'Update success !');

            return $this->redirectToRoute('show_list');
        }

        return $this->render('show/create.html.twig', ['showForm'=>$showForm->createView()]);
    }

    /**
     * @Route("/update", name="_list_update")
     */
    public function listupdateAction()
    {
        $repo = $this->getDoctrine()->getRepository(Show::class);
        $show = $repo->findAll();
        //dump($show);die;
        return $this->render('show/list_update.html.twig',['show'=>$show]);
    }

    /**
     * @Route(name="_search")
     */
    public function searchAction(Request $request)
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        return $this->render('_includes/search.html.twig',['showForm'=>$form->createView()]);
    }

    /**
     * @Route("/find", name="_find")
     */
    public function findAction(Request $request)
    {
        $find = $request->request->get('search');
        $repo = $this->getDoctrine()->getRepository(Show::class);
        $show = $repo->findBy(
            ['name' => $find['name']]
        );
        if(empty($show)) $this->addFlash('error', 'No show find sorry...');
        return $this->render('show/list.html.twig',['show' => $show]);
    }

    /**
     * @Route("/delete", name="_delete")
     */
    public function deleteAction(Request $request)
    {
        $doctrine = $this->getDoctrine();
        $delete = $request->request->get('show_id');
        $repo = $doctrine->getRepository(Show::class);
        dump($doctrine);
        $show = $repo->findOneById($delete);

        if(empty($show)){
            throw new NotFoundHttpException(sprintf('error'));
        }

        $doctrine->getManager()->remove($show);
        $doctrine->getManager()->flush();

        $this->addFlash('success','Delete success');
        return $this->redirectToRoute('show_list');

    }
}
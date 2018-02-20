<?php
/**
 * Created by PhpStorm.
 * User: digital
 * Date: 20/02/2018
 * Time: 11:36
 */

namespace AppBundle\Controller\API;

use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Categories;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class CategoryController
 * @package AppBundle\Controller\API
 * @Route(name="api_categories_")
 */
class CategoryController extends Controller
{
    /**
     * @Method({"GET"})
     * @Route("/categories", name="list")
     */
    public function listAction(SerializerInterface $serializer)
    {
        $categories = $this->getDoctrine()->getRepository('AppBundle:Categories')->findAll();

        $data = $serializer->serialize($categories,'json');

        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application\json']);
    }

    /**
     * @Method({"GET"})
     * @Route("/categories/{id}", name="get")
     */
    public function getAction(Categories $categories, SerializerInterface $serializer)
    {
        $data = $serializer->serialize($categories,'json');

        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application\json']);
    }
}
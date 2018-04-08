<?php

namespace App\Controller\Api;

use App\Controller\WithPaginationTrait;
use App\Entity\City;
use App\Entity\Station;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CityApiController extends Controller implements ApiControllerInterface
{
    use WithPaginationTrait;

    /**
     * Gets all the cities for a given page and limit.
     * The limit should stay between 1 and 100.
     *
     * @Route("/cities", name="api_get_cities", methods={"GET"})
     *
     * @return City[]
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function getCities(Request $request): array
    {
        $page = $this->getPage($request);
        $limit = $this->getLimit($request);

        return $this->getDoctrine()
            ->getRepository('App:City')
            ->findPage($page, $limit);
    }

}

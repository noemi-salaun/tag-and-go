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

/**
 * Stations API controller.
 *
 * @author Noémi Salaün <noemi.salaun@gmail.com>
 */
class StationApiController extends Controller implements ApiControllerInterface
{
    use WithPaginationTrait;

    /**
     * Gets all the stations of a city, for a given page and limit.
     * The limit should stay between 1 and 100.
     *
     * @Route("/stations/{cityId}", name="api_get_stations", methods={"GET"}, requirements={"cityId"="\d+"})
     * @ParamConverter("city", class="App:City", options={"id" = "cityId"})
     *
     * @return Station[]
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function getStations(Request $request, City $city): array
    {
        // Handle deactivated city like it does not exist.
        if (!$city->isActivated()) {
            throw $this->createNotFoundException('City not found');
        }

        $page = $this->getPage($request);
        $limit = $this->getLimit($request);

        return $this->getDoctrine()
            ->getRepository('App:Station')
            ->findPage($city, $page, $limit);
    }

    /**
     * Gets all the stations near some given coordinates.
     * The radius should stay between 1 and 100km.
     *
     * @Route("/stations/near", name="api_get_stations_near", methods={"GET"})
     *
     * @return Station[]
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function getNearStations(Request $request): array
    {
        $latitude = $request->query->get('lat');
        $longitude = $request->query->get('lng');

        // Check parameters validity.
        if (null === $latitude || null === $longitude) {
            throw new BadRequestHttpException('Parameters "lat" and "lng" are required.');
        }

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            throw new BadRequestHttpException('Parameters "lat" and "lng" should be numerics.');
        }

        $latitude = (float)$latitude;
        $longitude = (float)$longitude;

        $radius = $request->query->get('radius', 10);

        // Set the max radius to 100 to prevent heavy fetching.
        if (!is_numeric($radius) || $radius <= 0 || $radius > 100) {
            throw new BadRequestHttpException('Parameter radius should be a positive integer under 100.');
        }

        $radius = (int)$radius;

        return $this->getDoctrine()
            ->getRepository('App:Station')
            ->findNear($latitude, $longitude, $radius);
    }

    /**
     * Take a bike from a station.
     *
     * @Route("/stations/{id}/take", name="api_take_bike", methods={"POST"})
     */
    public function takeBike(Station $station): bool
    {
        if ($station->getBikesAvailable() <= 0) {
            return false;
        }

        $station->decrementBikesAvailable();
        $this->getDoctrine()->getManager()->flush();

        return true;
    }

    /**
     * Drop a bike in a station.
     *
     * @Route("/stations/{id}/drop", name="api_drop_bike", methods={"POST"})
     */
    public function dropBike(Station $station): bool
    {
        if ($station->getBikesAvailable() >= $station->getBikesCapacity()) {
            return false;
        }

        $station->incrementBikesAvailable();
        $this->getDoctrine()->getManager()->flush();

        return true;
    }

}

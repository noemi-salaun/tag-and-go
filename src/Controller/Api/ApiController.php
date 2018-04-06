<?php

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Station;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends Controller
{

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Gets all the cities for a given page and limit.
     * The limit should stay between 1 and 100.
     *
     * @Route("/cities", name="api_get_cities", methods={"GET"})
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function getCities(Request $request): JsonResponse
    {
        $page = $this->getPage($request);
        $limit = $this->getLimit($request);

        $cities = $this->getDoctrine()
            ->getRepository('App:City')
            ->findPage($page, $limit);

        return $this->getJsonResponse($cities, ['read_city']);
    }

    /**
     * Gets all the stations of a city, for a given page and limit.
     * The limit should stay between 1 and 100.
     *
     * @Route("/stations/{cityId}", name="api_get_stations", methods={"GET"}, requirements={"cityId"="\d+"})
     * @ParamConverter("city", class="App:City", options={"id" = "cityId"})
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    public function getStations(Request $request, City $city): JsonResponse
    {
        // Handle deactivated city like it does not exist.
        if (!$city->isActivated()) {
            throw $this->createNotFoundException('City not found');
        }

        $page = $this->getPage($request);
        $limit = $this->getLimit($request);

        $stations = $this->getDoctrine()
            ->getRepository('App:Station')
            ->findPage($city, $page, $limit);

        return $this->getJsonResponse($stations, ['read_station']);
    }

    /**
     * Gets all the stations near some given coordinates.
     * The radius should stay between 1 and 100km.
     *
     * @Route("/stations/near", name="api_get_stations_near", methods={"GET"})
     */
    public function getNearStations(Request $request): JsonResponse
    {
        $latitude = $request->query->get('latitude');
        $longitude = $request->query->get('longitude');

        if (null === $latitude || null === $longitude) {
            throw new BadRequestHttpException('Parameters latitude and longitude are required.');
        }

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            throw new BadRequestHttpException('Parameters latitude and longitude should be numerics.');
        }

        $latitude = (float)$latitude;
        $longitude = (float)$longitude;

        $radius = $request->query->get('radius', 10);

        // Set the max radius to 100 to prevent heavy fetching.
        if (!is_numeric($radius) || $radius <= 0 || $radius > 100) {
            throw new BadRequestHttpException('Parameter radius should be a positive integer under 100.');
        }

        $radius = (int)$radius;

        $stations = $this->getDoctrine()
            ->getRepository('App:Station')
            ->findNear($latitude, $longitude, $radius);

        return $this->getJsonResponse($stations, ['read_station']);
    }

    /**
     * Take a bike from a station.
     *
     * @Route("/stations/{id}/take", name="api_take_bike", methods={"POST"})
     */
    public function takeBike(Station $station): JsonResponse
    {
        if ($station->getBikesAvailable() <= 0) {
            return new JsonResponse(['result' => false]);
        }

        $station->decrementBikesAvailable();
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(['result' => true]);
    }

    /**
     * Drop a bike in a station.
     *
     * @Route("/stations/{id}/drop", name="api_drop_bike", methods={"POST"})
     */
    public function dropBike(Station $station): JsonResponse
    {
        if ($station->getBikesAvailable() >= $station->getBikesCapacity()) {
            return new JsonResponse(['result' => false]);
        }

        $station->incrementBikesAvailable();
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(['result' => true]);
    }

    /**
     * Extracts the page parameter from the request and checks its validity.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    private function getPage(Request $request): int
    {
        $page = $request->query->get('page', 0);
        if (!is_numeric($page) || $page < 0) {
            throw new BadRequestHttpException('Parameter "page" should be a positive integer.');
        }
        return (int)$page;
    }

    /**
     * Extracts the limit parameter from the request and checks its validity.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     */
    private function getLimit(Request $request): int
    {
        $limit = $request->query->get('limit', 30);
        // Set the max limit to 100 to prevent heavy fetching.
        if (!is_numeric($limit) || $limit < 1 || $limit > 100) {
            throw new BadRequestHttpException('Parameter "limit" should be a positive integer under 100.');
        }
        return (int)$limit;
    }

    /**
     * Create a new JsonResponse with data serialized using context groups.
     */
    private function getJsonResponse($data, array $groups): JsonResponse
    {
        // Use the ATOM date format because the ISO-8601 is deprecate.
        // See https://secure.php.net/manual/en/class.datetime.php#datetime.constants.cookie
        return new JsonResponse(
            $this->serializer->serialize($data, 'json', [
                'groups' => $groups,
                DateTimeNormalizer::FORMAT_KEY => \DateTime::ATOM,
            ]),
            Response::HTTP_OK,
            [],
            true
        );
    }

}

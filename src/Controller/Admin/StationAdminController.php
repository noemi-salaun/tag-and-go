<?php

namespace App\Controller\Admin;

use App\Entity\Station;
use App\Form\StationType;
use App\Repository\StationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/stations")
 */
class StationAdminController extends Controller
{

    /**
     * @Route("", name="admin_station_index", methods="GET")
     */
    public function index(StationRepository $stationRepository): Response
    {
        return $this->render('admin/station/index.html.twig', ['stations' => $stationRepository->findAll()]);
    }

    /**
     * @Route("/new", name="admin_station_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(StationType::class);
        $form->handleRequest($request);
        /** @var Station $station */
        $station = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($station);
            $em->flush();

            return $this->redirectToRoute('admin_station_index');
        }

        return $this->render(
            'admin/station/new.html.twig',
            [
                'station' => $station,
                'form'    => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="admin_station_show", methods="GET")
     */
    public function show(Station $station): Response
    {
        return $this->render('admin/station/show.html.twig', ['station' => $station]);
    }

    /**
     * @Route("/{id}/edit", name="admin_station_edit", methods="GET|POST")
     */
    public function edit(Request $request, Station $station): Response
    {
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_station_edit', ['id' => $station->getId()]);
        }

        return $this->render(
            'admin/station/edit.html.twig',
            [
                'station' => $station,
                'form'    => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="admin_station_delete", methods="DELETE")
     */
    public function delete(Request $request, Station $station): Response
    {
        if ($this->isCsrfTokenValid('delete'.$station->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($station);
            $em->flush();
        }

        return $this->redirectToRoute('admin_station_index');
    }
}

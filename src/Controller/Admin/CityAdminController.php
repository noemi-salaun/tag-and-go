<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * CRUD controller for cities.
 *
 * @Route("/cities")
 *
 * @author Noémi Salaün <noemi.salaun@gmail.com>
 */
class CityAdminController extends Controller
{
    /**
     * Display all the cities.
     *
     * @Route("", name="admin_city_index", methods="GET")
     */
    public function index(CityRepository $cityRepository): Response
    {
        return $this->render('admin/city/index.html.twig', ['cities' => $cityRepository->findAll()]);
    }

    /**
     * Handle the creation of a new city.
     *
     * @Route("/new", name="admin_city_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(CityType::class);
        $form->handleRequest($request);
        /** @var City $city */
        $city = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->flush();

            return $this->redirectToRoute('admin_city_index');
        }

        return $this->render('admin/city/new.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Display city details.
     *
     * @Route("/{id}", name="admin_city_show", methods="GET")
     */
    public function show(City $city): Response
    {
        return $this->render('admin/city/show.html.twig', ['city' => $city]);
    }

    /**
     * Handle the edition of a city.
     *
     * @Route("/{id}/edit", name="admin_city_edit", methods="GET|POST")
     */
    public function edit(Request $request, City $city): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_city_edit', ['id' => $city->getId()]);
        }

        return $this->render('admin/city/edit.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete the given city.
     *
     * @Route("/{id}", name="admin_city_delete", methods="DELETE")
     */
    public function delete(Request $request, City $city): Response
    {
        if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($city);
            $em->flush();
        }

        return $this->redirectToRoute('admin_city_index');
    }
}

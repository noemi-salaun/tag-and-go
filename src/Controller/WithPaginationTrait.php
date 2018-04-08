<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Trait that adds the capacity to extract pagination or limitation parameters from the request.
 *
 * @author Noémi Salaün <noemi.salaun@gmail.com>
 */
trait WithPaginationTrait
{

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
}
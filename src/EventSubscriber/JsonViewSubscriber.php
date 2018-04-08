<?php

namespace App\EventSubscriber;

use App\Controller\Api\ApiControllerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Subscriber that serialize and create a new JsonResponse for API controllers.
 *
 * @see \App\Controller\Api\ApiControllerInterface
 *
 * @author Noémi Salaün <noemi.salaun@gmail.com>
 */
class JsonViewSubscriber implements EventSubscriberInterface
{

    public const IS_API_FLAG = 'is_api';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var bool
     */
    private $isDebug;

    public function __construct(SerializerInterface $serializer, bool $isDebug)
    {
        $this->serializer = $serializer;
        $this->isDebug = $isDebug;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'checkForApiController',
            KernelEvents::VIEW => 'serializeData',
            KernelEvents::EXCEPTION => 'serializeError',
        ];
    }

    /**
     * Check if the controller is an API controller.
     */
    public function checkForApiController(FilterControllerEvent $event): void
    {
        $controller = $event->getController();

        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony but it may happen.
         * If it is a class, it comes in array format
         */
        if (!\is_array($controller)) {
            return;
        }

        if (!$controller[0] instanceof ApiControllerInterface) {
            return;
        }

        $event->getRequest()->attributes->set(self::IS_API_FLAG, true);
    }

    /**
     * Serialize the result of the controller into a nicely serialized JSON Response.
     */
    public function serializeData(GetResponseForControllerResultEvent $event): void
    {

        // check to see if checkForApiController marked this as an API request
        if (!$event->getRequest()->attributes->get(self::IS_API_FLAG, false)) {
            return;
        }

        $data = $event->getControllerResult();
        if (\is_scalar($data)) {
            $data = ['result' => $data];
        }

        $event->setResponse($this->getJsonResponse($data));
    }

    /**
     * Serialize an exception into a nice JSON response.
     */
    public function serializeError(GetResponseForExceptionEvent $event): void
    {
        // check to see if checkForApiController marked this as an API request
        if (!$event->getRequest()->attributes->get(self::IS_API_FLAG, false)) {
            return;
        }

        $exception = $event->getException();

        // Default to status code 500, or get the status code from the HTTP exception.
        $code = 500;
        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
        }

        $responseData = [
            'error' => [
                'code' => $code,
                'message' => $exception->getMessage()
            ]
        ];

        // To prevent leaking internal information, the stack trace is shown only in debug mode.
        if ($this->isDebug) {
            $responseData['error']['trace'] = $exception->getTrace();
        }

        $event->setResponse(new JsonResponse($responseData, $code));
    }

    /**
     * Create a new JsonResponse with data serialized using context groups.
     */
    private function getJsonResponse($data): JsonResponse
    {
        // Use the ATOM date format because the ISO-8601 is deprecate.
        // See https://secure.php.net/manual/en/class.datetime.php#datetime.constants.cookie
        return new JsonResponse(
            $this->serializer->serialize($data, 'json', [
                'groups' => ['read'],
                DateTimeNormalizer::FORMAT_KEY => \DateTime::ATOM,
            ]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}

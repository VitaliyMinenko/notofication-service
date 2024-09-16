<?php

namespace App\Controller;

use App\Dto\NotificationDto;
use App\Services\ProviderService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;

#[Route('/api/v1/notification', name: 'app_notification_')]
class NotificationController extends AbstractController
{
    public function __construct(private readonly ProviderService $providerService)
    {
    }

    /**
     * @throws Exception
     */
    #[Route('/', name: 'index', methods: ['POST'])]
    public function index(
        #[MapRequestPayload]
        NotificationDto $notificationDto
    ): JsonResponse {
        try {
            $this->providerService->process($notificationDto);
        } catch (Exception $exception) {
            return $this->json([
                'error' => $exception->getMessage(), 500
            ]);
        }
        return $this->json([
            'message' => 'Notification was add to process'
        ]);
    }
}

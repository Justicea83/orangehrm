<?php

namespace OrangeHRM\Core\Subscriber;

use Exception;
use OrangeHRM\Admin\Service\SubscriptionService;
use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ControllerTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SubscriptionSubscriber extends AbstractEventSubscriber
{
    use ServiceContainerTrait, AuthUserTrait, ControllerTrait;

    protected ?SubscriptionService $subscriptionService = null;

    public function getSubscriptionService()
    {
        if (!$this->subscriptionService instanceof SubscriptionService) {
            $this->subscriptionService = new SubscriptionService();
        }
        return $this->subscriptionService;

    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onRequestEvent', 1000],
            ],
        ];
    }

    public function onRequestEvent(RequestEvent $event): void
    {
        try {
            if ($event->isMainRequest()) {
                $subscription = $this->getSubscriptionService()->getActiveSubscription($this->getAuthUser()->getUserId());

                if (!$subscription) {
                    /** @var UrlGenerator $urlGenerator */
                    $urlGenerator = $this->getContainer()->get(Services::URL_GENERATOR);
                    $loginUrl = $urlGenerator->generate('auth_login', [], UrlGenerator::ABSOLUTE_URL);

                    /** @var Session $session */
                    $session = $this->getContainer()->get(Services::SESSION);

                    $session->invalidate();
                    $session->getFlashBag()->add(AuthUser::FLASH_LOGIN_ERROR, [
                        'message' => 'You do not have an active subscription, please contact your administrator'
                    ]);
                    $response = new RedirectResponse($loginUrl);
                    $event->setResponse($response);
                }
            }
        } catch (Exception $e) {

        }
    }
}
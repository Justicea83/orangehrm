<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Authentication\Controller;

use OrangeHRM\Admin\Service\OrganizationService;
use OrangeHRM\Authentication\Auth\AuthProviderChain;
use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Authentication\Dto\AuthParams;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Exception\AuthenticationException;
use OrangeHRM\Authentication\Service\LoginService;
use OrangeHRM\Authentication\Traits\CsrfTokenManagerTrait;
use OrangeHRM\Core\Authorization\Service\HomePageService;
use OrangeHRM\Core\Controller\AbstractController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Exception\RedirectableException;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use Throwable;

class ValidateController extends AbstractController implements PublicControllerInterface
{
    use AuthUserTrait;
    use ServiceContainerTrait;
    use CsrfTokenManagerTrait;

    public const PARAMETER_USERNAME = 'username';
    public const PARAMETER_PASSWORD = 'password';

    protected ?OrganizationService $organizationService = null;

    protected ?LoginService $loginService = null;

    protected ?HomePageService $homePageService = null;

    public function getHomePageService(): HomePageService
    {
        if (!$this->homePageService instanceof HomePageService) {
            $this->homePageService = new HomePageService();
        }
        return $this->homePageService;
    }

    public function getOrganizationService(): ?OrganizationService
    {
        if (!$this->organizationService instanceof OrganizationService) {
            $this->organizationService = new OrganizationService();
        }
        return $this->organizationService;
    }

    public function getLoginService(): LoginService
    {
        if (is_null($this->loginService)) {
            $this->loginService = new LoginService();
        }
        return $this->loginService;
    }

    public function handle(Request $request): RedirectResponse
    {
        $username = $request->request->get(self::PARAMETER_USERNAME, '');
        $password = $request->request->get(self::PARAMETER_PASSWORD, '');
        $credentials = new UserCredential($username, $password);

        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = $this->getContainer()->get(Services::URL_GENERATOR);
        $loginUrl = $urlGenerator->generate('auth_login', [], UrlGenerator::ABSOLUTE_URL);

        /** @var Session $session */
        $session = $this->getContainer()->get(Services::SESSION);

        try {
            $token = $request->request->get('_token');
            if (!$this->getCsrfTokenManager()->isValid('login', $token)) {
                throw AuthenticationException::invalidCsrfToken();
            }

            /** @var AuthProviderChain $authProviderChain */
            $authProviderChain = $this->getContainer()->get(Services::AUTH_PROVIDER_CHAIN);
            $success = $authProviderChain->authenticate(new AuthParams($credentials));

            if (!$success) {
                throw AuthenticationException::invalidCredentials();
            }
            $organization = $this->getOrganizationService()->findById($this->getAuthUser()->getOrgId());

            if (!$organization->isSetup()) {
                $session->invalidate();
                $session->getFlashBag()->add(AuthUser::FLASH_LOGIN_ERROR, [
                    'message' => 'We are currently setting you up, try again in a few minutes'
                ]);
                return new RedirectResponse($loginUrl);
            }

            $this->getAuthUser()->setIsAuthenticated($success);
            $this->getLoginService()->addLogin($credentials);
        } catch (AuthenticationException $e) {
            $this->getAuthUser()->addFlash(AuthUser::FLASH_LOGIN_ERROR, $e->normalize());
            if ($e instanceof RedirectableException) {
                return new RedirectResponse($e->getRedirectUrl());
            }
            return new RedirectResponse($loginUrl);
        } catch (Throwable $e) {
            $this->getAuthUser()->addFlash(
                AuthUser::FLASH_LOGIN_ERROR,
                [
                    'error' => AuthenticationException::UNEXPECT_ERROR,
                    'message' => 'Unexpected error occurred',
                ]
            );
            return new RedirectResponse($loginUrl);
        }

        //Recreate the session
        $session->migrate(true);

        if ($this->getAuthUser()->hasAttribute(AuthUser::SESSION_TIMEOUT_REDIRECT_URL)) {
            $redirectUrl = $this->getAuthUser()->getAttribute(AuthUser::SESSION_TIMEOUT_REDIRECT_URL);
            $this->getAuthUser()->removeAttribute(AuthUser::SESSION_TIMEOUT_REDIRECT_URL);
            $logoutUrl = $urlGenerator->generate('auth_logout', [], UrlGenerator::ABSOLUTE_URL);

            if ($redirectUrl !== $loginUrl || $redirectUrl !== $logoutUrl) {
                return new RedirectResponse($redirectUrl);
            }
        }

        $homePagePath = $this->getHomePageService()->getHomePagePath();
        return $this->redirect($homePagePath);
    }
}

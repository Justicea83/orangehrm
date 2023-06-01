<?php

namespace OrangeHRM\OAuth\Repository;

use Doctrine\ORM\NonUniqueResultException;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Admin\Service\OrganizationService;
use OrangeHRM\Authentication\Dto\AuthParams;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Core\Dao\BaseDao;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;
use OrangeHRM\OAuth\Dto\Entity\UserEntity;

class PasswordGrantRepository extends BaseDao implements UserRepositoryInterface
{
    use ServiceContainerTrait;

    protected ?OrganizationService $organizationService = null;

    public function getUserDao(): UserDao
    {
        return $this->userDao ??= new UserDao();
    }

    public function getOrganizationService(): ?OrganizationService
    {
        if (!$this->organizationService instanceof OrganizationService) {
            $this->organizationService = new OrganizationService();
        }
        return $this->organizationService;
    }

    /**
     * @throws NonUniqueResultException
     * @throws OAuthServerException
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity): UserEntity
    {
        $authProviderChain = $this->getContainer()->get(Services::AUTH_PROVIDER_CHAIN);

        $credentials = new UserCredential($username, $password);
        $success = $authProviderChain->authenticate(new AuthParams($credentials));

        if (!$success) {
            throw OAuthServerException::invalidCredentials();
        }

        $user = $this->getUserDao()->isExistingSystemUser($credentials);

        $organization = $this->getOrganizationService()->findById($user->getOrgId());

        if (!$organization->isSetup()) {
            throw OAuthServerException::invalidGrant('try again in a few minutes');
        }
        return UserEntity::createFromEntity($user);
    }
}

<?php

use OrangeHRM\Comments\Service\CommentService;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Framework\Services;

class CommentPluginConfiguration implements PluginConfigurationInterface
{
    use ServiceContainerTrait;

    public function initialize(Request $request): void
    {
        $this->getContainer()->register(
            Services::COMMENT_SERVICE,
            CommentService::class
        );
    }
}

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

namespace OrangeHRM\Pim\Controller;

use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Service\IDGeneratorService;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\EmpPicture;
use OrangeHRM\Framework\Http\Request;

class SaveEmployeeController extends AbstractVueController
{
    protected ?IDGeneratorService $idGeneratorService = null;

    /**
     * @return IDGeneratorService|null
     */
    public function getIdGeneratorService(): ?IDGeneratorService
    {
        if (!$this->idGeneratorService instanceof IDGeneratorService) {
            $this->idGeneratorService = new IDGeneratorService();
        }
        return $this->idGeneratorService;
    }

    /**
     * @return UserDao
     */
    public function getUserDao(): UserDao
    {
        return $this->userDao ??= new UserDao();
    }

    public function preRender(Request $request): void
    {
        $component = new Component('employee-save');
        $component->addProp(new Prop('user-roles', Prop::TYPE_ARRAY, $this->getUserDao()->getFormattedAssignableUserRoles()));
        $component->addProp(new Prop('allowed-image-types', Prop::TYPE_ARRAY, EmpPicture::ALLOWED_IMAGE_TYPES));
        $this->setComponent($component);
    }
}

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
import Login from './pages/Login.vue';
import Register from './pages/Register/Register.vue';
import Forbidden from './pages/Forbidden.vue';
import ResetPassword from './pages/ResetPassword.vue';
import ResetWeakPassword from './pages/ResetWeakPassword.vue';
import ResetPasswordError from './pages/ResetPasswordError.vue';
import AdministratorAccess from './pages/AdministratorAccess.vue';
import RequestResetPassword from './pages/RequestResetPassword.vue';
import ResetPasswordSuccess from './pages/ResetPasswordSuccess.vue';
import EmailConfigurationWarning from './pages/EmailConfigurationWarning.vue';
export default {
  'auth-login': Login,
  'auth-register': Register,
  'auth-forbidden': Forbidden,
  'reset-password': ResetPassword,
  'auth-admin-access': AdministratorAccess,
  'reset-weak-password': ResetWeakPassword,
  'reset-password-error': ResetPasswordError,
  'reset-password-success': ResetPasswordSuccess,
  'request-reset-password': RequestResetPassword,
  'email-configuration-warning': EmailConfigurationWarning,
};
//# sourceMappingURL=index.js.map

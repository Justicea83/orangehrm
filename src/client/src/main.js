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
import {createApp} from 'vue';
import components from './components';
import pages from './pages';
import mitt from 'mitt';
import acl from './core/plugins/acl/acl';
import toaster from './core/plugins/toaster/toaster';
import createI18n from './core/plugins/i18n/translate';
import '@ohrm/oxd/fonts.css';
import '@ohrm/oxd/icons.css';
import '@ohrm/oxd/style.css';
import './core/styles/global.scss';
import './core/plugins/toaster/toaster.scss';
import './core/plugins/loader/loader.scss';
const app = createApp({
  name: 'App',
  // eslint-disable-next-line @typescript-eslint/ban-ts-comment
  // @ts-ignore
  components: pages,
});
// setup global emitter
const emitter = mitt();
app.config.globalProperties.emitter = emitter;
// Global Register Components
app.use(components);
app.use(toaster, {
  duration: 2500,
  persist: false,
  animation: 'oxd-toast-list',
  position: 'bottom',
});
// @ts-expect-error: appGlobal is not in window object by default
const baseUrl = window.appGlobal.baseUrl;
const {i18n, init} = createI18n({
  baseUrl: baseUrl,
  resourceUrl: 'core/i18n/messages',
});
app.use(acl);
app.use(i18n);
app.config.globalProperties.global = {
  baseUrl,
};
init().then(() => app.mount('#app'));
//# sourceMappingURL=main.js.map

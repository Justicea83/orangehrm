<!--
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
 -->
<template>
  <oxd-form class="orangehrm-installer-page" @submit="toggleModal">
    <oxd-text tag="h5" class="orangehrm-installer-page-title">
      Welcome to OrangeHRM Starter Version {{ productversion }} Setup Wizard
    </oxd-text>
    <br />
    <oxd-text tag="p" class="orangehrm-installer-page-content">
      This setup wizard will guide through the steps necessary to complete the
      registration process.
    </oxd-text>
    <br />

    <oxd-text tag="p" class="orangehrm-installer-page-content">
      Click <b>Next</b> to continue
    </oxd-text>

    <oxd-form-actions class="orangehrm-installer-page-action">
      <oxd-button display-type="secondary" label="Next" type="submit" />
    </oxd-form-actions>
  </oxd-form>
  <database-config-dialog
    v-if="showModal"
    @close-model="closeModel"
  ></database-config-dialog>
</template>

<script>
import {navigate} from '@/core/util/helper/navigation.ts';
import DatabaseConfigDialog from '@/components/DatabaseConfigDialog.vue';
export default {
  name: 'WelcomeScreen',
  components: {
    'database-config-dialog': DatabaseConfigDialog,
  },
  props: {
    productversion: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      selected: 'install',
      showModal: false,
    };
  },
  methods: {
    toggleModal() {
      if (this.selected === 'install') {
        return navigate('/installer/licence-acceptance');
      }
      this.showModal = !this.showModal;
    },
    closeModel(isAccept) {
      if (!isAccept || this.selected !== 'upgrade') return this.toggleModal();
      navigate('/upgrader/database-config');
    },
  },
};
</script>

<style src="./installer-page.scss" lang="scss" scoped></style>
<style lang="scss" scoped>
::v-deep(.oxd-radio-wrapper label) {
  font-weight: 700;
  margin-left: -0.5rem;
}
</style>

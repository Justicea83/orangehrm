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
  <div class="orangehrm-installer-page">
    <oxd-text tag="h5" class="orangehrm-installer-page-title">
      Installation Complete
    </oxd-text>
    <br />
    <oxd-text tag="p" class="orangehrm-installer-page-content">
      OrangeHRM Starter Version {{ productversion }} has been successfully
      installed.
    </oxd-text>
    <br />
    <br />
    <oxd-button
      class="orangehrm-installer-complete-button"
      display-type="secondary"
      label="Launch OrangeHRM"
      @click="launch"
    />
  </div>
</template>

<script>
export default {
  name: 'InstallerCompleteScreen',
  props: {
    productversion: {
      type: String,
      required: true,
    },
  },
  methods: {
    launch() {
      window.location.href = window.location.origin;
    },
  },
};
</script>

<style src="./installer-page.scss" lang="scss" scoped></style>
<style lang="scss" scoped>
.orangehrm-installer-complete-button {
  margin-right: auto;
}
</style>

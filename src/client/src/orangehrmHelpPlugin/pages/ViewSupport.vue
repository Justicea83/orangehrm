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
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        Getting Started with TaskflowHR
      </oxd-text>
      <oxd-divider />
      <oxd-text tag="p" class="orangehrm-support-text">
        Learning how to use a new application can be challenging. At TaskflowHR,
        we are committed to providing you with the necessary knowledge and
        skills required to fully utilize the application thereby allowing you to
        quickly and efficiently manage your HR processes.
      </oxd-text>
      <br />
      <oxd-text tag="p" class="orangehrm-support-text">
        The following information repositories are available to help you
        understand the application:
      </oxd-text>
      <div class="orangehrm-support">
        <oxd-grid :cols="2" class="orangehrm-support-channel --mail">
          <oxd-grid-item>
            <img :src="helpImgSrc" alt="help image" />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-text class="orangehrm-sub-title" tag="p">
              {{ $t('help.customer_support') }}
            </oxd-text>
            <oxd-text tag="p" class="orangehrm-support-text">
              {{ $t('help.contact_us_on_message') }}
              <a
                class="orangehrm-support-link"
                href="mailto:support@taskflowhr.com"
              >
                support@taskflowhr.com
              </a>
              {{ $t('help.we_will_be_delighted_to_help') }}
            </oxd-text>
          </oxd-grid-item>
        </oxd-grid>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      helpImgSrc: `${window.appGlobal.publicPath}/images/help.png`,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-support-text {
  font-size: 12px;
}
.orangehrm-support {
  padding: 1rem 0;
  &-channel {
    grid-template-columns: 50px 1fr;
    align-items: center;
    img {
      width: 40px;
      height: 40px;
    }
  }
  &-link {
    text-decoration: none;
    color: $oxd-primary-one-color;
  }
}
</style>

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
  <div class="oxd-table-card-cell">
    <div class="data">
      <oxd-text tag="p">
        {{ text }}
      </oxd-text>
    </div>
  </div>
</template>

<script>
import {useInjectTableProps} from '@ohrm/oxd';

export default {
  name: 'FrequencyCell',

  props: {
    header: {
      type: Object,
      required: true,
    },
    text: {
      type: String,
      required: true,
    },
  },

  setup() {
    const {screenState} = useInjectTableProps();

    return {
      screenState,
    };
  },

  computed: {
    showHeader() {
      return !(
        this.screenState.screenType === 'lg' ||
        this.screenState.screenType === 'xl'
      );
    },
  },
};
</script>

<style lang="scss" scoped>
.oxd-table-card-cell {
  display: block;
  & .header {
    font-weight: 700;
  }
  & .time {
    white-space: nowrap;
  }
}
</style>

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
  <oxd-input-field
    :type="multiple ? 'multiselect' : 'select'"
    :label="$t('general.sub_unit')"
    :options="options"
  />
</template>

<script>
import {ref, onBeforeMount} from 'vue';
import {APIService} from '@ohrm/core/util/services/api.service';
import {OxdInputField} from '@ohrm/oxd';

export default {
  name: 'SubunitDropdown',
  components: {
    OxdInputField,
  },
  props: {
    multiple: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  setup() {
    const options = ref([]);
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/admin/subunits',
    );
    onBeforeMount(() => {
      http.getAll().then(({data}) => {
        options.value = data.data
          .filter((item) => item.level > 0)
          .map((item) => {
            return {
              id: item.id,
              label: item.name,
              _indent: item.level ? item.level + 1 : 1,
            };
          });
      });
    });
    return {
      options,
    };
  },
};
</script>

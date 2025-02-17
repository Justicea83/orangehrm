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
    type="autocomplete"
    :label="$t('general.employee_name')"
    :clear="false"
    :create-options="loadEmployees"
  >
    <template #afterSelected="{data}">
      <template v-if="data.isPastEmployee">
        {{ $t('general.past_employee') }}
      </template>
    </template>
    <template #option="{data}">
      <span>{{ data.label }}</span>
      <div v-if="data.isPastEmployee" class="past-employee-tag">
        {{ $t('general.past_employee') }}
      </div>
    </template>
  </oxd-input-field>
</template>

<script>
import {APIService} from '@ohrm/core/util/services/api.service';
import {OxdInputField} from '@ohrm/oxd';

export default {
  name: 'EmployeeAutocomplete',
  components: {
    OxdInputField,
  },
  props: {
    params: {
      type: Object,
      default: () => ({}),
    },
    apiPath: {
      type: String,
      default: '/api/v2/pim/employees',
    },
  },
  setup(props) {
    const http = new APIService(window.appGlobal.baseUrl, props.apiPath);
    return {
      http,
    };
  },
  methods: {
    async loadEmployees(serachParam) {
      return new Promise((resolve) => {
        if (serachParam.trim()) {
          this.http
            .getAll({
              nameOrId: serachParam.trim(),
              ...this.params,
            })
            .then(({data}) => {
              resolve(
                data.data.map((employee) => {
                  return {
                    id: employee.empNumber,
                    label: `${employee.firstName} ${employee.middleName} ${employee.lastName}`,
                    _employee: employee,
                    isPastEmployee: !!employee.terminationId,
                  };
                }),
              );
            });
        } else {
          resolve([]);
        }
      });
    },
  },
};
</script>

<style scoped>
.past-employee-tag {
  margin-left: auto;
}
</style>

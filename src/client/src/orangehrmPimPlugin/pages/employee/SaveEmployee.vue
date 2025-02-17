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
        {{ $t('general.add_employee') }}
      </oxd-text>
      <oxd-divider />

      <oxd-form :loading="isLoading" @submit-valid="onSave">
        <div class="orangehrm-employee-container">
          <div class="orangehrm-employee-image">
            <profile-image-input
              v-model="employee.empPicture"
              :rules="rules.empPicture"
              :img-src="profilePicUrl"
            />
          </div>
          <div class="orangehrm-employee-form">
            <oxd-form-row>
              <oxd-grid :cols="1" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <full-name-input
                    v-model:firstName="employee.firstName"
                    v-model:middleName="employee.middleName"
                    v-model:lastName="employee.lastName"
                    :rules="rules"
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>
            <oxd-divider />
            <oxd-form-row class="user-form-header">
              <oxd-text class="user-form-header-text" tag="p">
                {{ $t('pim.create_login_details') }}
              </oxd-text>
              <oxd-switch-input v-model="createLogin" />
            </oxd-form-row>

            <template v-if="createLogin">
              <oxd-form-row>
                <oxd-grid :cols="2" class="orangehrm-full-width-grid">
                  <oxd-grid-item>
                    <oxd-input-field
                      v-model="user.username"
                      :label="$t('general.username')"
                      :rules="rules.username"
                      required
                      autocomplete="off"
                    />
                  </oxd-grid-item>

                  <oxd-grid-item>
                    <oxd-input-field
                      v-model="user.role"
                      type="select"
                      :label="$t('general.user_role')"
                      :rules="rules.role"
                      :options="userRoles"
                      required
                    />
                  </oxd-grid-item>
                </oxd-grid>
              </oxd-form-row>

              <password-input
                v-model:password="user.password"
                v-model:passwordConfirm="user.passwordConfirm"
              />
            </template>
          </div>
        </div>

        <oxd-divider />
        <oxd-form-actions>
          <required-text />
          <oxd-button
            display-type="ghost"
            :label="$t('general.cancel')"
            @click="onCancel"
          />
          <submit-button />
        </oxd-form-actions>
      </oxd-form>
    </div>
  </div>
</template>

<script>
import {ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {navigate} from '@ohrm/core/util/helper/navigation';
import ProfileImageInput from '@/orangehrmPimPlugin/components/ProfileImageInput';
import FullNameInput from '@/orangehrmPimPlugin/components/FullNameInput';
import PasswordInput from '@/core/components/inputs/PasswordInput';
import {
  maxFileSize,
  required,
  shouldNotExceedCharLength,
  shouldNotLessThanCharLength,
  validFileTypes,
} from '@ohrm/core/util/validation/rules';
import {OxdSwitchInput} from '@ohrm/oxd';

const defaultPic = `${window.appGlobal.publicPath}/images/default-photo.png`;

const employeeModel = {
  firstName: '',
  middleName: '',
  lastName: '',
  empPicture: null,
  employeeId: '',
};

const userModel = {
  username: '',
  //userRoleId: 2,
  role: null,
  empNumber: 0,
  status: '1',
  password: '',
  passwordConfirm: '',
};

export default {
  components: {
    'oxd-switch-input': OxdSwitchInput,
    'profile-image-input': ProfileImageInput,
    'full-name-input': FullNameInput,
    'password-input': PasswordInput,
  },

  props: {
    empId: {
      type: String,
      required: true,
    },
    allowedImageTypes: {
      type: Array,
      required: true,
    },
    userRoles: {
      type: Array,
      required: true,
    },
  },

  setup(props) {
    const employee = ref({
      ...employeeModel,
      employeeId: props.empId ? props.empId : '',
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/pim/employees',
    );

    return {
      http,
      employee,
    };
  },

  data() {
    return {
      isLoading: false,
      createLogin: false,
      user: {...userModel},
      empNumber: null,
      rules: {
        firstName: [required, shouldNotExceedCharLength(30)],
        middleName: [shouldNotExceedCharLength(30)],
        lastName: [required, shouldNotExceedCharLength(30)],
        employeeId: [shouldNotExceedCharLength(10)],
        role: [required],
        empPicture: [
          maxFileSize(1024 * 1024),
          validFileTypes(this.allowedImageTypes),
        ],
        username: [
          required,
          shouldNotLessThanCharLength(5),
          shouldNotExceedCharLength(40),
        ],
        status: [required],
      },
    };
  },

  computed: {
    profilePicUrl() {
      if (this.employee.empPicture) {
        const file = this.employee.empPicture.base64;
        const type = this.employee.empPicture.type;
        const isPicture = this.allowedImageTypes.findIndex(
          (item) => item === type,
        );
        return isPicture > -1 ? `data:${type};base64,${file}` : defaultPic;
      } else {
        return defaultPic;
      }
    },
  },

  created() {
    this.isLoading = true;
    this.http
      .getAll()
      .then((response) => {
        const {data} = response.data;
        this.rules.employeeId.push((v) => {
          const index = data.findIndex(
            (item) =>
              item.employeeId?.trim() &&
              String(item.employeeId).toLowerCase() == String(v).toLowerCase(),
          );
          if (index > -1) {
            return this.$t('pim.employee_id_exists');
          } else {
            return true;
          }
        });
        return this.http.request({
          method: 'GET',
          url: '/api/v2/admin/users',
        });
      })
      .then((response) => {
        const {data} = response.data;
        this.rules.username.push((v) => {
          const index = data.findIndex(
            (item) =>
              String(item.userName).toLowerCase() == String(v).toLowerCase(),
          );
          if (index > -1) {
            return this.$t('pim.username_already_exists');
          } else {
            return true;
          }
        });
      })
      .finally(() => {
        this.isLoading = false;
      });
  },

  methods: {
    onCancel() {
      navigate('/pim/viewEmployeeList');
    },
    onSave() {
      this.isLoading = true;
      this.http
        .create({
          ...this.employee,
        })
        .then((response) => {
          const {data} = response;
          if (data?.data) {
            this.empNumber = data.data.empNumber;
          }
          if (this.createLogin && data?.data) {
            return this.http.http.post('api/v2/admin/users', {
              username: this.user.username,
              password: this.user.password,
              status: this.user.status === '1',
              userRoleId: this.user.role?.id,
              empNumber: data.data.empNumber,
            });
          } else {
            return;
          }
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .then(() => {
          this.employee = {...employeeModel};
          this.user = {...userModel};
          if (this.empNumber) {
            navigate(`/pim/viewPersonalDetails/empNumber/${this.empNumber}`);
          } else {
            this.onCancel();
          }
        })
        .catch(() => {
          this.isLoading = false;
        });
    },
  },
};
</script>

<style src="./employee.scss" lang="scss" scoped></style>

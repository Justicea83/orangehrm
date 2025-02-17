<template>
  <div>
    <sync-zk-teco
      :config="serverConfig"
      @server-config-changed="serverConfig = $event"
    />
    <div class="orangehrm-background-container">
      <div class="orangehrm-card-container">
        <div class="orangehrm-header-container">
          <oxd-text tag="h6" class="orangehrm-main-title">
            ZkTeco Configuration
          </oxd-text>
          <oxd-switch-input
            v-if="!isDisabled"
            v-model="configuration.enable"
            label-position="left"
            :option-label="$t('general.enable')"
          />
        </div>
        <oxd-divider />

        <template v-if="!isDisabled">
          <oxd-form ref="formRef" :loading="isLoading">
            <oxd-text tag="p" class="orangehrm-subtitle">
              {{ $t('admin.server_settings') }}
            </oxd-text>
            <oxd-form-row>
              <oxd-grid :cols="3" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="configuration.scheme"
                    type="select"
                    :show-empty-selector="false"
                    :options="schemes"
                    label="Scheme"
                  />
                </oxd-grid-item>
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="configuration.hostname"
                    :label="$t('admin.host')"
                    :rules="rules.hostname"
                    required
                  />
                </oxd-grid-item>
                <oxd-grid-item class="orangehrm-column-half">
                  <oxd-input-field
                    v-model="configuration.port"
                    :label="$t('admin.port')"
                    :rules="rules.port"
                    required
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>

            <oxd-divider class="orangehrm-form-divider" />

            <oxd-text tag="p" class="orangehrm-subtitle">
              Authentication Settings
            </oxd-text>

            <oxd-form-row>
              <oxd-grid :cols="3" class="orangehrm-full-width-grid">
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="configuration.adminUsername"
                    label="Admin Username"
                    :rules="rules.adminUsername"
                    required
                  />
                </oxd-grid-item>
                <oxd-grid-item>
                  <oxd-input-field
                    v-model="configuration.adminPassword"
                    type="password"
                    label="Admin Password"
                    :placeholder="passwordPlaceHolder"
                    :rules="rules.adminPassword"
                    :required="!configuration.hasadminPassword"
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>

            <oxd-divider class="orangehrm-form-divider" />

            <oxd-text tag="p" class="orangehrm-subtitle mb-2">
              {{ $t('admin.additional_settings') }}
            </oxd-text>

            <oxd-form-row>
              <oxd-grid :cols="3" class="orangehrm-full-width-grid">
                <oxd-grid-item class="orangehrm-ldap-switch --offset-row-1">
                  <oxd-text tag="p" class="orangehrm-ldap-switch-text">
                    Override Salary
                  </oxd-text>
                  <oxd-switch-input v-model="configuration.overrideSalary" />
                </oxd-grid-item>
                <oxd-grid-item class="--offset-row-2">
                  <oxd-input-field
                    v-model="configuration.syncInterval"
                    label="Sync Interval (in Days)"
                    :rules="rules.syncInterval"
                    required
                  />
                </oxd-grid-item>
              </oxd-grid>
            </oxd-form-row>

            <oxd-divider class="orangehrm-form-divider" />

            <!--  Salary Components      -->
            <div
              class="orangehrm-horizontal-padding orangehrm-vertical-padding"
            >
              <profile-action-header action-button-shown @click="onClickAdd">
                {{ $t('pim.assigned_salary_components') }}
              </profile-action-header>
            </div>
            <table-header
              :selected="checkedItems.length"
              :total="salaries.length"
              :loading="isLoading"
              @delete="onClickDeleteSelected"
            />
            <oxd-card-table
              v-model:selected="checkedItems"
              :headers="tableHeaders"
              :items="salaries"
              selectable
              :clickable="false"
              :loading="isLoading"
              row-decorator="oxd-table-decorator-card"
            />
            <delete-confirmation ref="deleteDialog"></delete-confirmation>

            <oxd-divider />

            <oxd-form-actions>
              <oxd-button
                type="button"
                display-type="ghost"
                :label="$t('admin.test_connection')"
                @click="onClickTest"
              />
              <oxd-button
                type="button"
                class="orangehrm-left-space"
                display-type="secondary"
                :label="$t('general.save')"
                @click="onClickSave"
              />
            </oxd-form-actions>
          </oxd-form>
        </template>

        <save-zk-teco-salary-component
          v-if="showSaveModal"
          :http="http"
          :paygrades="paygrades"
          :pay-frequencies="payFrequencies"
          :currencies="currencies"
          @close="onSaveModalClose"
        />

        <edit-zk-teco-salary-component
          v-if="showEditModal"
          :http="http"
          :data="editModalState"
          :paygrades="paygrades"
          :pay-frequencies="payFrequencies"
          :currencies="currencies"
          @close="onEditModalClose"
        />
      </div>

      <zk-teco-test-connection-modal
        v-if="testModalState"
        :status="testModalState"
        @close="onCloseTestModal"
      />

      <br />

      <force-sync-zk-teco
        v-if="serverConfig?.enabled && !serverConfig?.syncing"
        :config="serverConfig"
        @server-config-changed="serverConfig = $event"
      />
    </div>
  </div>
</template>

<script>
import {
  OxdButton,
  OxdCardTable,
  OxdDivider,
  OxdForm,
  OxdFormActions,
  OxdFormRow,
  OxdGrid,
  OxdText,
  OxdGridItem,
  OxdSwitchInput,
  OxdInputField,
} from '@ohrm/oxd';
import {
  digitsOnly,
  numberShouldBeBetweenMinAndMaxValue,
  required,
  shouldNotExceedCharLength,
  validHostnameFormat,
  validPortRange,
} from '@/core/util/validation/rules';
import ProfileActionHeader from '@/orangehrmPimPlugin/components/ProfileActionHeader.vue';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import {APIService} from '@/core/util/services/api.service';
import useForm from '@/core/util/composable/useForm';
import {reloadPage} from '@/core/util/helper/navigation';
import ZkTecoTestConnectionModal from '@/zktecoPlugin/pages/configure/connection/components/ZkTecoTestConnectionModal.vue';
import SaveZkTecoSalaryComponent from '@/zktecoPlugin/pages/configure/connection/components/SaveZkTecoSalaryComponent.vue';
import FrequencyCell from '@/zktecoPlugin/pages/configure/connection/components/FrequencyCell.vue';
import EditZkTecoSalaryComponent from '@/zktecoPlugin/pages/configure/connection/components/EditZkTecoSalaryComponent.vue';
import SyncZkTeco from '@/zktecoPlugin/pages/configure/connection/components/SyncZkTeco.vue';
import ForceSyncZkTeco from '@/zktecoPlugin/pages/configure/connection/components/ForceSyncZkTeco.vue';
import TableHeader from '@/core/components/table/TableHeader.vue';

const configurationModel = {
  enable: false,
  overrideSalary: false,
  hostname: 'localhost',
  port: 80,
  scheme: 'http',
  adminUsername: null,
  adminPassword: null,
  syncInterval: 1,
};

export default {
  name: 'CreateConnection',
  components: {
    TableHeader,
    'profile-action-header': ProfileActionHeader,
    'oxd-switch-input': OxdSwitchInput,
    'delete-confirmation': DeleteConfirmationDialog,
    ZkTecoTestConnectionModal,
    SaveZkTecoSalaryComponent,
    EditZkTecoSalaryComponent,
    OxdForm,
    OxdFormActions,
    OxdFormRow,
    OxdGrid,
    OxdGridItem,
    OxdDivider,
    OxdButton,
    OxdCardTable,
    SyncZkTeco,
    OxdText,
    OxdInputField,
    ForceSyncZkTeco,
  },
  props: {
    paygrades: {
      type: Array,
      default: () => [],
    },
    payFrequencies: {
      type: Array,
      default: () => [],
    },
    currencies: {
      type: Array,
      default: () => [],
    },
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/zkteco/config',
    );
    const {formRef, invalid, validate} = useForm();

    return {
      http,
      formRef,
      invalid,
      validate,
    };
  },
  data() {
    return {
      intervalId: null,
      serverConfig: null,
      editModalState: null,
      testModalState: null,
      showSyncAlert: true,
      schemes: [
        {
          id: 'http',
          label: 'Http',
        },
        {
          id: 'https',
          label: 'Https',
        },
      ],
      isLoading: false,
      configuration: {
        ...configurationModel,
      },
      rules: {
        hostname: [
          required,
          validHostnameFormat,
          shouldNotExceedCharLength(255),
        ],
        port: [required, validPortRange(5, 0, 65535)],
        adminUsername: [required, shouldNotExceedCharLength(255)],
        adminPassword: [(v) => required(v), shouldNotExceedCharLength(255)],
        syncInterval: [
          required,
          digitsOnly,
          numberShouldBeBetweenMinAndMaxValue(1, 23),
        ],
      },
      salaries: [],
      headers: [
        {
          name: 'salaryComponent',
          slot: 'title',
          title: this.$t('pim.salary_component'),
          style: {flex: 1},
        },
        {name: 'salaryAmount', title: this.$t('pim.amount'), style: {flex: 1}},
        {
          name: 'payGradeId',
          title: this.$t('pim.pay_frequency'),
          style: {flex: 1},
          cellRenderer: this.frequencyCellRenderer,
        },
      ],
      checkedItems: [],
      showSaveModal: false,
      showEditModal: false,
    };
  },
  computed: {
    isDisabled() {
      return this.showSaveModal || this.showEditModal;
    },
    tableHeaders() {
      const headerActions = {
        name: 'actions',
        slot: 'action',
        title: this.$t('general.actions'),
        style: {flex: 1},
        cellType: 'oxd-table-cell-actions',
        cellConfig: {},
      };
      headerActions.cellConfig.delete = {
        onClick: this.onClickDelete,
        component: 'oxd-icon-button',
        props: {
          name: 'trash',
        },
      };
      headerActions.cellConfig.edit = {
        onClick: this.onClickEdit,
        props: {
          name: 'pencil-fill',
        },
      };
      return Object.keys(headerActions.cellConfig).length > 0
        ? this.headers.concat([headerActions])
        : this.headers;
    },
  },
  watch: {},
  beforeMount() {
    this.isLoading = true;
    this.fetchConfig().finally(() => (this.isLoading = false));
  },
  methods: {
    fetchConfig() {
      return this.http.getAll().then((response) => {
        const {data} = response.data;

        if (!data) {
          return;
        }

        this.serverConfig = data;
        this.configuration.enable = data.enabled;
        this.configuration.hostname = data.host;
        this.configuration.port = data.port;
        this.salaries = data.salaries ?? [];
        this.configuration.scheme = {
          id: data.scheme?.toString()?.toLowerCase(),
          label: data.scheme,
        };
        this.configuration.adminUsername = data.adminUsername;
        this.configuration.adminPassword = data.adminPassword;
        this.configuration.syncInterval = data.syncInterval;
        this.configuration.overrideSalary = data.overrideSalary;
      });
    },
    onClickEdit(item) {
      this.showSaveModal = false;
      this.editModalState = item;
      this.showEditModal = true;
    },
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems([item.id]);
        }
      });
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map((index) => {
        return this.salaries[index].id;
      });
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    deleteItems(items) {
      if (items instanceof Array) {
        this.isLoading = true;
        this.http
          .request({
            method: 'DELETE',
            url: '/api/v2/zkteco/salary-components',
            data: {
              ids: items,
            },
          })
          .then(() => {
            return this.$toast.deleteSuccess();
          })
          .then(() => {
            this.isLoading = false;
            reloadPage();
          });
      }
    },
    frequencyCellRenderer(...args) {
      const cellData = this.salaries[args[0]];
      const frequency = this.payFrequencies.find(
        (freq) => freq.id === cellData.payFrequencyId,
      );
      return {
        component: FrequencyCell,
        props: {
          text: frequency?.label ?? '',
        },
      };
    },
    getRequestBody() {
      return {
        enable: this.configuration.enable,
        host: this.configuration.hostname,
        scheme: this.configuration.scheme?.id,
        overrideSalary: this.configuration.overrideSalary,
        port: parseInt(this.configuration.port),
        adminUsername: this.configuration.adminUsername,
        adminPassword: this.configuration.adminPassword,
        syncInterval: parseInt(this.configuration.syncInterval),
      };
    },
    getTestConnectionRequestBody() {
      return {
        host: this.configuration.hostname,
        scheme: this.configuration.scheme?.id,
        port: parseInt(this.configuration.port),
        adminUsername: this.configuration.adminUsername,
        adminPassword: this.configuration.adminPassword,
      };
    },
    onClickSave() {
      this.validate().then(() => {
        if (this.invalid === true) return;
        this.isLoading = true;
        this.http
          .request({
            method: 'PUT',
            data: this.getRequestBody(),
          })
          .then(() => {
            return this.$toast.updateSuccess();
          })
          .finally(() => reloadPage());
      });
    },
    onCloseTestModal() {
      this.testModalState = null;
    },
    onClickTest() {
      this.validate().then(() => {
        if (this.invalid === true) return;
        this.isLoading = true;
        const data = this.getTestConnectionRequestBody();
        this.http
          .request({
            method: 'POST',
            url: '/api/v2/zkteco/test-connection',
            data,
          })
          .then((response) => {
            const {data} = response.data;
            this.testModalState = data?.status;
          })
          .finally(() => (this.isLoading = false));
      });
    },
    onClickAdd() {
      this.showEditModal = false;
      this.editModalState = null;
      this.showSaveModal = true;
    },
    onSaveModalClose() {
      this.showSaveModal = false;
      this.resetDataTable();
    },
    onEditModalClose() {
      this.showEditModal = false;
      this.editModalState = null;
      this.resetDataTable();
    },
    onDismissSyncAlert() {
      this.showSyncAlert = false;
    },
  },
};
</script>

<style src="./zkteco-configuration.scss" lang="scss" scoped></style>

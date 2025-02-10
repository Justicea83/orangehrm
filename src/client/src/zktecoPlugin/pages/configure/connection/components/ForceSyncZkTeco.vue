<template>
  <div class="orangehrm-paper-container">
    <div class="orangehrm-header-container">
      <div class="orangehrm-ldap-sync">
        <oxd-text tag="h6" class="orangehrm-main-title">
          {{ $t('admin.sync_connection') }}
        </oxd-text>
        <oxd-text
          v-show="lastSync"
          type="card-body"
          class="orangehrm-ldap-sync-time"
        >
          ({{ lastSync }})
        </oxd-text>
      </div>
      <oxd-loading-spinner
        v-if="isLoading"
        class="orangehrm-ldap-sync-loader"
      />
      <oxd-button
        v-else
        display-type="secondary"
        class="orangehrm-ldap-sync-button"
        :label="$t('admin.sync_now')"
        @click="onClickSync"
      />
    </div>
  </div>
</template>

<script lang="ts">
import {APIService} from '@/core/util/services/api.service';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {OxdSpinner, OxdText, OxdButton} from '@ohrm/oxd';
import {PropType} from 'vue';
import {format} from 'date-fns';
import {zonedTimeToUtc, utcToZonedTime} from 'date-fns-tz';
import {ServerConfig} from '@/zktecoPlugin/pages/configure/connection/types/serverConfig';
import {AxiosResponse} from "axios";
import {da} from "date-fns/locale";

export default {
  name: 'ForceSyncZkTeco',
  components: {
    'oxd-loading-spinner': OxdSpinner,
    OxdText,
    OxdButton,
  },
  props: {
    config: {
      type: Object as PropType<ServerConfig>,
      default: () => null,
    },
  },
  emits: ['serverConfigChanged'],
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/zkteco/force-sync',
    );
    const {jsDateFormat} = useDateFormat();

    return {
      http,
      jsDateFormat,
    };
  },
  data() {
    return {
      isLoading: false,
      serverConfig: null as ServerConfig | null,
      lastSyncDate: null,
      lastSyncTime: null,
      lastSyncStatus: null,
    };
  },
  computed: {
    lastSync() {
      console.clear();
      console.log(this.serverConfig);
      if (!this.serverConfig?.lastSync) return null;

      try {
        const {date, timezone} = this.serverConfig.lastSync;
        const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        // Convert from the stored timezone (e.g., "Pacific/Auckland") to UTC
        const utcDate = zonedTimeToUtc(date, timezone);

        // Convert UTC to the user's local timezone
        const localDate = utcToZonedTime(utcDate, userTimezone);

        return this.$t('admin.last_synced_on_datetime', {
          datetime: format(localDate, `hh:mm a ${this.jsDateFormat}`),
        });
      } catch (error) {
        return null;
      }
    },
  },
  watch: {
    config: {
      handler(val: ServerConfig | null) {
        if (val) {
          this.serverConfig = JSON.parse(JSON.stringify(val)) as ServerConfig;
        }
      },
      deep: true,
      immediate: true,
    },
  },
  methods: {
    onClickSync() {
      this.isLoading = true;
      this.http
        .create()
        .then((response: AxiosResponse) => {
          const data = response.data.data as ServerConfig | null;

          if(data) {
            this.serverConfig = data;
            this.$emit('serverConfigChanged', data);
          }
          this.$toast.success({
            title: this.$t('general.success'),
            message: this.$t('admin.synchronization_successful'),
          });
        })
        .catch(() => {
          this.getLastSyncStatus();
          this.$toast.error({
            title: this.$t('general.error'),
            message: this.$t('admin.synchronization_failed'),
          });
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-ldap-sync {
  display: flex;
  flex-direction: column;
  @include oxd-respond-to('md') {
    flex-direction: row;
    align-items: center;
    &-time {
      margin-left: 1rem;
    }
  }
  &-button {
    white-space: normal !important;
  }
  &-loader {
    margin: 0 2rem;
  }
}
</style>

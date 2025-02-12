<template>
  <div v-if="serverConfig?.syncing" style="margin-bottom: 1em">
    <oxd-toast
      class="margin-toast"
      :show="showAlert"
      type="info"
      title="Sync In Progress"
      message="Some data may be missing during syncing periods."
      @update:show="handleDismiss($event)"
    />
    <oxd-progressbar
      type="main"
      stripe
      animation
      :show-label="false"
      :progress="100"
    />
  </div>
</template>

<script lang="ts">
import {OxdProgressbar, OxdToast} from '@ohrm/oxd';
import {PropType} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {ServerConfig} from '@/zktecoPlugin/pages/configure/connection/types/serverConfig';
import {AxiosResponse} from 'axios';

export default {
  name: 'SyncZkTeco',
  components: {
    OxdProgressbar,
    OxdToast,
  },
  props: {
    config: {
      type: Object as PropType<ServerConfig>,
      default: () => null,
    },
  },
  emits: ['onDismissAlert', 'serverConfigChanged'],
  setup() {
    const http = new APIService(
      (window as any).appGlobal.baseUrl,
      '/api/v2/zkteco/config',
    );

    return {
      http,
    };
  },
  data() {
    return {
      serverConfig: null as ServerConfig | null,
      showAlert: true,
    };
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
    serverConfig: {
      handler(val: ServerConfig, oldVal: ServerConfig) {
        if (val?.syncing) {
          // Clear existing interval if already running
          if (this.intervalId) {
            clearInterval(this.intervalId);
          }

          // Start a new interval
          this.intervalId = setInterval(() => this.fetchConfig(), 10000);
        } else {
          // If syncing is false and was previously true, clear the interval and show toast
          if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
          }

          if (oldVal?.syncing) {
            // Ensure toast only shows when sync state changes from true to false
            this.$toast.success({
              title: this.$t('general.success'),
              message: 'Sync Completed',
            });
          }
        }
      },
      deep: true, // Ensure nested properties like `syncing` are observed
    },
  },
  mounted() {
    if (!this.serverConfig) {
      this.fetchConfig();
    }
  },
  methods: {
    handleDismiss() {
      this.showAlert = false;
    },
    fetchConfig() {
      return this.http.getAll().then((response: AxiosResponse) => {
        const data = response.data.data as ServerConfig;

        if (!data) {
          return;
        }

        this.serverConfig = data;
        this.$emit('serverConfigChanged', data);
      });
    },
  },
};
</script>

<style scoped lang="scss">
.margin-toast {
  margin-bottom: 1rem;
}
</style>

<template>
  <div class="oxd-layout">
    <div class="oxd-layout-navigation">
      <oxd-side-panel
        :toggle="collapse"
        :home-url="homeUrl"
        :brand-logo-src="brandLogoSrc"
        :menu-items="sidepanelMenuItems"
        :brand-banner-src="brandBannerSrc"
        @collapse="onCollapse"
      >
      </oxd-side-panel>
      <oxd-top-bar
        :user="user"
        :toggle="collapse"
        :breadcrumb="breadcrumb"
        :menu-items="topbarMenuItems"
        @collapse="onCollapse"
      >
        <template #user-context>
          <slot name="user-actions"></slot>
        </template>
        <template #nav-context>
          <slot name="nav-actions"></slot>
        </template>
      </oxd-top-bar>
    </div>
    <div :class="containerClasses">
      <oxd-overlay
        class="oxd-layout-overlay"
        :show="collapse"
        @click="onCollapse"
      ></oxd-overlay>
      <div class="oxd-layout-context">
        <slot></slot>
      </div>
      <div class="oxd-layout-footer">
        <slot name="footer"></slot>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import type {PropType} from 'vue';
import {defineComponent} from 'vue';
import type MenuItem from '../SidePanel/types';
import {OxdTopbar, OxdOverlay} from '@ohrm/oxd';
import SidePanel from '../SidePanel/SidePanel.vue';
import type {
  TopMenuItem,
  User,
  Breadcrumb,
} from '@ohrm/oxd/types/components/Topbar/types';

export default defineComponent({
  name: 'AppLayout',

  components: {
    'oxd-top-bar': OxdTopbar,
    'oxd-overlay': OxdOverlay,
    'oxd-side-panel': SidePanel,
  },

  props: {
    user: {
      type: Object as PropType<User>,
      required: true,
    },
    sidepanelMenuItems: {
      type: Object as PropType<MenuItem[]>,
      required: true,
    },
    topbarMenuItems: {
      type: Object as PropType<TopMenuItem[]>,
      required: true,
    },
    breadcrumb: {
      type: Object as PropType<Breadcrumb>,
      required: true,
    },
    brandLogoSrc: {
      type: String,
      required: true,
    },
    brandBannerSrc: {
      type: String,
      required: true,
    },
    homeUrl: {
      type: String,
      required: false,
      default: '/',
    },
  },

  data() {
    return {
      collapse: false,
    };
  },

  computed: {
    containerClasses(): object {
      return {
        'oxd-layout-container': true,
        '--collapse': this.collapse,
      };
    },
  },

  mounted() {
    console.log('base mounted 1');
  },

  methods: {
    onCollapse() {
      this.collapse = !this.collapse;
    },
  },
});
</script>

<style src="./layout.scss" lang="scss" scoped></style>
<style src="./layout-global.scss" lang="scss"></style>

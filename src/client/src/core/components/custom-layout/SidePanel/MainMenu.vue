<template>
  <!-- Search -->
  <div class="oxd-main-menu --fixed">
    <div class="oxd-main-menu-search">
      <div class="flex flex-row justify-center">
        <oxd-icon
          name="search"
          type="svg"
          width="22"
          height="22"
          class="oxd-menu-icon"
        ></oxd-icon>
        <oxd-input
          v-model="searchTerm"
          placeholder="Search"
          :class="{toggled: toggle}"
        />
      </div>
      <oxd-icon-button
        :name="toggle ? 'chevron-right' : 'chevron-left'"
        class="oxd-main-menu-button"
        role="none"
        @click="onClickCollapse"
      />
    </div>
    <hr class="oxd-main-menu-divider" />
  </div>
  <!-- Search -->

  <!-- Nav Items  -->
  <ul class="oxd-main-menu">
    <oxd-main-menu-item
      v-for="(menuItem, index) in filteredMenuItems"
      :key="`oxd-main-menu-${index}`"
      :url="menuItem.url"
      :active="menuItem.active"
      :collapsed="toggle"
      :name="menuItem.name"
      :icon-type="menuItem?.iconType"
      :icon="menuItem.icon"
    ></oxd-main-menu-item>
  </ul>
</template>

<script lang="ts">
import type {PropType} from 'vue';
import {defineComponent} from 'vue';
import type MenuItem from './types';
import MainMenuItem from './MainMenuItem.vue';
import {OxdIcon, OxdInput, OxdIconButton} from '@ohrm/oxd';

import usei18n from '@/core/util/composable/usei18n';

export default defineComponent({
  name: 'OxdMainMenu',

  components: {
    'oxd-icon': OxdIcon,
    'oxd-input': OxdInput,
    'oxd-icon-button': OxdIconButton,
    'oxd-main-menu-item': MainMenuItem,
  },

  props: {
    url: {
      type: String,
      required: false,
      default: '/',
    },
    toggle: {
      type: Boolean,
      required: false,
      default: false,
    },
    menuItems: {
      type: Array as PropType<MenuItem[]>,
      required: false,
      default: () => [],
    },
  },

  emits: ['collapse'],

  setup() {
    return {
      ...usei18n(),
    };
  },

  data() {
    return {
      searchTerm: '',
    };
  },

  computed: {
    classes(): object {
      return {
        toggled: this.toggle,
      };
    },
    filteredMenuItems(): MenuItem[] {
      const escapedSearchTerm = this.searchTerm.replace(
        /[/\-\\^$*+?.()|[\]{}]/g,
        '\\$&',
      );
      const filter = new RegExp(escapedSearchTerm, 'i');
      return this.menuItems.filter((item: MenuItem) => item.name.match(filter));
    },
  },

  methods: {
    onClickCollapse() {
      this.$emit('collapse');
      // trigger resize event for body resize event listners
      // timeout delay is added for sidebar animation 0.3s
      setTimeout(() => {
        window.dispatchEvent(new Event('resize'));
      }, 350);
    },
  },
});
</script>

<style src="./main-menu.scss" lang="scss" scoped></style>

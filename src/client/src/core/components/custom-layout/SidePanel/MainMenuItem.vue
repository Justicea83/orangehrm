<!--
/*
 * This file is part of OrangeHRM Inc
 *
 * Copyright (C) 2020 onwards OrangeHRM Inc
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see  http://www.gnu.org/licenses
 */
-->

<template>
  <li class="oxd-main-menu-item-wrapper">
    <a :class="menuClasses" :href="url">
      <oxd-icon
        :name="icon"
        :type="iconType || 'svg'"
        width="22"
        height="22"
        class="oxd-main-menu-item--icon"
        :class="{'text-bold': iconType === 'bootstrap'}"
      ></oxd-icon>
      <oxd-text tag="span" class="oxd-main-menu-item--name">
        {{ name }}
      </oxd-text>
    </a>
  </li>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import {OxdText, OxdIcon} from '@ohrm/oxd';

export default defineComponent({
  name: 'OxdMainMenuItem',

  components: {
    'oxd-text': OxdText,
    'oxd-icon': OxdIcon,
  },

  props: {
    url: {
      type: String,
      required: false,
      default: '#',
    },
    active: {
      type: Boolean,
      required: false,
      default: false,
    },
    collapsed: {
      type: Boolean,
      required: false,
      default: false,
    },
    name: {
      type: String,
      required: false,
      default: '',
    },
    iconType: {
      type: String,
      required: false,
      default: null,
    },
    icon: {
      type: String,
      required: false,
      default: 'home',
    },
  },

  computed: {
    menuClasses(): object {
      return {
        'oxd-main-menu-item': true,
        active: this.active,
        toggle: this.collapsed,
      };
    },
  },
});
</script>

<style src="./main-menu.scss" lang="scss" scoped></style>

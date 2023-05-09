<template>
  <Menu as="div" class="relative z-10">
    <MenuButton>
      <slot name="icon"></slot>
    </MenuButton>

    <transition
      enter-active-class="transition transform duration-100 ease-out"
      enter-from-class="opacity-0 scale-90"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition transform duration-100 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-90"
    >
      <MenuItems
        class="origin-top-left md:origin-top-left mt-1 focus:outline-none absolute right-0 md:left-0 border-gray-300 bg-white overflow-hidden rounded-md shadow-lg border w-40"
      >
        <MenuItem
          v-for="(menuItem, index) in menuItems"
          v-slot="{active}"
          :key="index"
          class="cursor-pointer"
          @click="$emit('menuItemClicked', menuItem)"
        >
          <a
            :class="{'bg-gray-100': active}"
            class="block px-4 py-2 text-sm text-gray-700"
            >{{ menuItem.title }}</a
          >
        </MenuItem>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script>
import {Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue';
export default {
  name: 'DropdownMenu',
  components: {Menu, MenuButton, MenuItems, MenuItem},
  props: {
    menuItems: {
      type: Array,
      required: true,
    },
  },
  emits: ['menuItemClicked'],
};
</script>

<style scoped></style>

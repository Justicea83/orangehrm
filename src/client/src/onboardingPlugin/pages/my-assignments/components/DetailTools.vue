<template>
  <div class="flex justify-between p-2 items-center">
    <dropdown-menu
      v-if="!taskGroup.submittedAt && isOwner"
      :menu-items="menuItems"
      @menu-item-clicked="menuItemClicked"
    >
      <template #icon>
        <ellipsis-vertical-icon class="h-6" />
      </template>
    </dropdown-menu>
    <h1 v-else></h1>
    <oxd-icon-button name="arrow-bar-right" @click="onClose" />
  </div>
</template>

<script>
import {OxdIconButton} from '@ohrm/oxd';
import {EllipsisVerticalIcon} from '@heroicons/vue/24/solid';
import DropdownMenu from '@/core/components/dropdown/DropdownMenu';

const MENU_COMPLETE = 'Mark Complete';
const MENU_SUBMIT = 'Submit';

export default {
  name: 'DetailTools',
  components: {
    OxdIconButton,
    EllipsisVerticalIcon,
    DropdownMenu,
  },
  props: {
    taskGroup: {
      type: Object,
      required: true,
    },
    isOwner: {
      type: Boolean,
      default: true,
    },
  },
  emits: ['onToggleMarkComplete', 'onClose', 'onSubmit'],
  computed: {
    menuItems() {
      if (this.taskGroup.completed) {
        return [
          {
            title: MENU_SUBMIT,
          },
        ];
      }
      return [
        {
          title: MENU_COMPLETE,
        },
        {
          title: MENU_SUBMIT,
        },
      ];
    },
  },
  methods: {
    onToggleMarkComplete() {
      this.$emit('onToggleMarkComplete');
    },
    onClose() {
      this.$emit('onClose');
    },
    menuItemClicked({title}) {
      switch (title) {
        case MENU_COMPLETE:
          this.$emit('onToggleMarkComplete');
          break;

        case MENU_SUBMIT:
          this.$emit('onSubmit');
          break;
      }
    },
  },
};
</script>

<style scoped></style>

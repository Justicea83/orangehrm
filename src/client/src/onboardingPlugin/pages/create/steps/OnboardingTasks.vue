<template>
  <div class="board-wrapper p-2 w-full">
    <div class="flex items-center justify-center w-full">
      <div class="board-group inline-flex h-full">
        <div class="flex flex-col w-full">
          <!--    header      -->
          <div class="board-group-header">
            <h4>All Tasks</h4>

            <dropdown-menu
              :menu-items="dropdownMenuItems"
              @menuItemClicked="tasksMenuItemClicked"
            >
              <template #icon>
                <button type="button" class="gray-background">
                  <EllipsisHorizontalIcon class="icon w-5 h-5" />
                </button>
              </template>
            </dropdown-menu>
          </div>

          <!--    Board Tasks      -->
          <div class="board-group-body">
            <draggable
              v-model="tasks"
              item-key="id"
              group="tasks"
              tag="ul"
              drag-class="drag"
              ghost-class="ghost"
            >
              <template #item="{element}">
                <task-list-item :task="element" />
              </template>
            </draggable>
          </div>
        </div>
      </div>

      <div class="board-group inline-flex h-full">
        <div class="flex flex-col w-full">
          <!--    header      -->
          <div class="board-group-header">
            <h4>ELP Tasks</h4>
            <dropdown-menu
              :menu-items="dropdownMenuItems"
              @menuItemClicked="elpTasksMenuItemClicked"
            >
              <template #icon>
                <button type="button" class="gray-background">
                  <EllipsisHorizontalIcon class="icon w-5 h-5" />
                </button>
              </template>
            </dropdown-menu>
          </div>

          <!--    Board Tasks      -->
          <div class="board-group-body">
            <draggable
              v-model="elpTasks"
              group="tasks"
              tag="ul"
              item-key="id"
              class="empty-drag-container"
              drag-class="drag"
              ghost-class="ghost"
            >
              <template #item="{element}">
                <task-list-item :task="element" />
              </template>
            </draggable>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {EllipsisHorizontalIcon} from '@heroicons/vue/24/solid';
import Draggable from 'vuedraggable';
import DropdownMenu from '@/core/components/dropdown/DropdownMenu';
import TaskListItem from '@/onboardingPlugin/pages/create/components/task-list-item/TaskListItem';

const MENU_ITEM_MOVE_ALL = 'Move all';

export default {
  name: 'OnboardingTasks',
  components: {
    EllipsisHorizontalIcon,
    Draggable,
    DropdownMenu,
    TaskListItem,
  },
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/onboarding/tasks',
    );
    return {
      http,
    };
  },
  data() {
    return {
      tasks: [],
      elpTasks: [],
      menuItems: ['Reset', 'Move All'],
      dropdownMenuItems: [
        {
          title: MENU_ITEM_MOVE_ALL,
        },
      ],
    };
  },
  mounted() {
    if (localStorage.getItem('tasks')) {
      this.tasks = JSON.parse(localStorage.getItem('tasks'));
      console.log(this.tasks);
    } else {
      this.http.getAll().then(({data}) => {
        localStorage.setItem('tasks', JSON.stringify(data.data));
      });
    }
  },
  methods: {
    elpTasksMenuItemClicked({title}) {
      switch (title) {
        case MENU_ITEM_MOVE_ALL: {
          this.tasks = [...this.elpTasks];
          this.elpTasks = [];
        }
      }
    },
    tasksMenuItemClicked({title}) {
      switch (title) {
        case MENU_ITEM_MOVE_ALL: {
          this.elpTasks = [...this.tasks];
          this.tasks = [];
        }
      }
    },
  },
};
</script>

<style src="./onboard-tasks.scss" scoped lang="scss"></style>

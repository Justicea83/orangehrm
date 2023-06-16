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
              @menu-item-clicked="tasksMenuItemClicked"
            >
              <template #icon>
                <button type="button" class="gray-background">
                  <EllipsisHorizontalIcon class="icon w-5 h-5" />
                </button>
              </template>
            </dropdown-menu>
          </div>

          <!--    Board Tasks      -->
          <div
            v-infinite-scroll="loadMoreTasks"
            class="board-group-body"
            infinite-scroll-distance="10"
            :infinite-scroll-disabled="loadMore"
          >
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
            <h4>Boarding Tasks</h4>
            <dropdown-menu
              :menu-items="dropdownMenuItems"
              @menu-item-clicked="elpTasksMenuItemClicked"
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
              @change="boardingTasksChanged"
            >
              <template #item="{element}">
                <task-list-item
                  :task="element"
                  @task-detail-changed="emitTasksChanged"
                />
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
import infiniteScroll from 'vue3-infinite-scroll-good';

const MENU_ITEM_MOVE_ALL = 'Move all';

export default {
  name: 'OnboardingTasks',
  directives: {infiniteScroll},
  components: {
    EllipsisHorizontalIcon,
    Draggable,
    DropdownMenu,
    TaskListItem,
  },
  props: {
    type: {
      type: Array,
      default: null,
    },
    data: {
      type: Object,
      default: null,
    },
  },
  emits: ['tasksChanged'],
  setup() {
    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/task-management/tasks',
    );
    return {
      http,
    };
  },
  data() {
    return {
      tasks: [],
      meta: {},
      elpTasks: [],
      loadMore: false,
      hasMoreTasks: true,
      menuItems: ['Reset', 'Move All'],
      dropdownMenuItems: [
        {
          title: MENU_ITEM_MOVE_ALL,
        },
      ],
    };
  },
  watch: {
    data(newData) {
      const elpTasksIds = this.elpTasks.map((elpTask) => elpTask.id);
      const {data, meta} = newData;
      this.meta = meta;
      this.hasMoreTasks = true;

      if (elpTasksIds.length > 0) {
        this.tasks = [...data].filter((task) => !elpTasksIds.includes(task.id));
      } else {
        this.tasks = [...data];
      }
    },
  },
  methods: {
    loadMoreTasks() {
      this.hasMoreTasks =
        this.meta.total > this.tasks?.length + this.elpTasks.length;

      if (this.type && this.hasMoreTasks) {
        this.http
          .request({
            params: {
              taskTypes: this.type?.map((t) => t.id).join(','),
              offset: this.tasks.length + this.elpTasks.length,
            },
          })
          .then(({data}) => {
            const {data: tasks, meta} = data;

            const elpTasksIds = this.elpTasks.map((elpTask) => elpTask.id);

            this.tasks = [
              ...this.tasks,
              ...tasks.filter((task) => !elpTasksIds.includes(task.id)),
            ];
            this.meta = meta;
          });
      }
    },
    emitTasksChanged() {
      this.$emit('tasksChanged', this.elpTasks);
    },
    boardingTasksChanged() {
      this.emitTasksChanged();
    },
    elpTasksMenuItemClicked({title}) {
      switch (title) {
        case MENU_ITEM_MOVE_ALL: {
          this.tasks = [...this.tasks, ...this.elpTasks];
          this.elpTasks = [];
        }
      }
    },
    tasksMenuItemClicked({title}) {
      switch (title) {
        case MENU_ITEM_MOVE_ALL: {
          this.elpTasks = [...this.elpTasks, ...this.tasks];
          this.tasks = [];

          this.emitTasksChanged();
        }
      }
    },
  },
};
</script>

<style src="./onboard-tasks.scss" scoped lang="scss"></style>

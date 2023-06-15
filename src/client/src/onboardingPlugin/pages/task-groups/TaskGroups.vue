<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-button
          :label="$t('general.add')"
          icon-name="plus"
          display-type="secondary"
          @click="onClickAdd"
        />
      </div>
      <table-header
        :selected="checkedItems.length"
        :total="total"
        :loading="isLoading"
        @delete="onClickDeleteSelected"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          ref="cardTable"
          v-model:selected="checkedItems"
          v-model:order="sortDefinition"
          :headers="headers"
          :items="taskGroups?.data"
          :selectable="true"
          :clickable="true"
          :loading="isLoading"
          class="orangehrm-employee-list"
          row-decorator="oxd-table-decorator-card"
          @click="onClickEdit"
        />
      </div>
      <div class="orangehrm-bottom-container">
        <oxd-pagination
          v-if="showPaginator"
          v-model:current="currentPage"
          :length="pages"
        />
      </div>
    </div>
    <delete-confirmation ref="deleteDialog"></delete-confirmation>
  </div>
</template>

<script lang="ts">
import {APIService} from '@/core/util/services/api.service';
import {computed, ref} from 'vue';
import useSort, {SortDefinition} from '@/core/util/composable/useSort';
import usePaginate from '@/core/util/composable/usePaginate';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';
import {OxdCardTable, OxdButton, OxdPagination} from '@ohrm/oxd';
import TableHeader from '@ohrm/components/table/TableHeader.vue';
import {CardHeader} from '@ohrm/oxd/types/components/CardTable/types';
import DeadlineDate from '@/onboardingPlugin/pages/task-groups/components/DeadlineDate.vue';
import CompleteCell from '@/onboardingPlugin/pages/task-groups/components/CompleteCell.vue';
import NameCell from '@/onboardingPlugin/pages/task-groups/components/NameCell.vue';
import {TaskGroup} from '@/onboardingPlugin/models';
import {navigate} from '@/core/util/helper/navigation';
const defaultSortOrder: SortDefinition = {
  'task.title': 'DEFAULT',
};

interface RowItem {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  [key: string]: any;
  isSelectable?: boolean;
  isDisabled?: boolean;
}

export default {
  name: 'TaskGroups',
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    OxdCardTable,
    TableHeader,
    OxdPagination,
    OxdButton,
  },
  setup() {
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const filters = ref({
      name: '',
    });

    const serializedFilters = computed(() => {
      return {
        name: filters.value.name,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      };
    });

    const http = new APIService(
      (window as any).appGlobal.baseUrl,
      '/api/v2/onboarding/task-assignments',
    );

    const {
      showPaginator,
      currentPage,
      total,
      pages,
      response,
      isLoading,
      execQuery,
    } = usePaginate(http, {
      query: serializedFilters,
    });

    onSort(execQuery);

    return {
      http,
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      execQuery,
      taskGroups: response,
      filters,
      sortDefinition,
    };
  },
  data() {
    return {
      checkedItems: [],
    };
  },
  computed: {
    headers() {
      return [
        {
          name: 'name',
          slot: 'name',
          title: 'Name',
          sortField: 't.name',
          style: {flex: 1},
        },
        {
          name: 'startDate',
          slot: 'startDate',
          title: 'Start Date',
          sortField: 't.startDate',
          style: {flex: 1},
        },
        {
          name: 'endDate',
          slot: 'endDate',
          title: 'End Date',
          sortField: 't.endDate',
          style: {flex: 1},
          cellRenderer: this.endDateCellRenderer,
        },
        {
          name: 'dueDate',
          slot: 'dueDate',
          title: 'Due Date',
          sortField: 't.dueDate',
          style: {flex: 1},
          cellRenderer: this.dueDateCellRenderer,
        },
        {
          name: 'completed',
          slot: 'completed',
          title: 'Completed',
          sortField: 't.completed',
          style: {flex: 1},
          cellRenderer: this.renderCompletedCell,
        },
        {
          name: 'submittedAt',
          slot: 'submittedAt',
          title: 'Submitted At',
          sortField: 't.submittedAt',
          style: {flex: 1},
        },
        {
          name: 'creator',
          slot: 'creator',
          title: 'Creator',
          style: {flex: 1},
          cellRenderer: this.renderCreatorName,
        },
        {
          name: 'supervisor',
          slot: 'supervisor',
          title: 'Supervisor',
          style: {flex: 1},
          cellRenderer: this.renderSupervisorName,
        },
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
          style: {flex: 1},
          cellType: 'oxd-table-cell-actions',
          cellConfig: {
            delete: {
              onClick: this.onClickDelete,
              component: 'oxd-icon-button',
              props: {
                name: 'trash',
              },
            },
            edit: {
              onClick: this.onClickEdit,
              props: {
                name: 'pencil-fill',
              },
            },
          },
        },
      ];
    },
  },
  methods: {
    onClickAdd() {
      navigate('/onboarding/createTasks');
    },
    onClickEdit() {
      //navigate('/admin/saveTask');
    },
    onClickDelete(item: TaskGroup) {
      this.$refs.deleteDialog.showDialog().then((confirmation: string) => {
        if (confirmation === 'ok') {
          this.deleteItems([item.id]);
        }
      });
    },
    async filterItems() {
      await this.execQuery();
    },
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    deleteItems(items: number[]) {
      this.isLoading = true;
      this.http
        .deleteAll({
          ids: items,
        })
        .then(() => {
          return this.$toast.deleteSuccess();
        })
        .then(() => {
          this.isLoading = false;
          this.resetDataTable();
        });
    },
    onClickDeleteSelected() {
      const ids: number[] = [];
      this.checkedItems.forEach((index: number) => {
        ids.push(this.taskGroups?.data[index].id);
      });
      this.$refs.deleteDialog.showDialog().then((confirmation: string) => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    dueDateCellRenderer(
      index: number,
      item: RowItem,
      header: CardHeader,
      rowItem: RowItem,
    ) {
      return this.renderDateCell(index, item, header, rowItem, 'dueDate');
    },
    endDateCellRenderer(
      index: number,
      item: RowItem,
      header: CardHeader,
      rowItem: RowItem,
    ) {
      return this.renderDateCell(index, item, header, rowItem, 'endDate');
    },
    renderDateCell(
      index: number,
      item: RowItem,
      header: CardHeader,
      rowItem: RowItem,
      prop: string,
    ) {
      return {
        component: DeadlineDate,
        props: {
          date: rowItem[prop],
          isComplete: rowItem.completed,
        },
      };
    },
    renderCompletedCell(
      index: number,
      item: RowItem,
      header: CardHeader,
      rowItem: RowItem,
    ) {
      return {
        component: CompleteCell,
        props: {
          isComplete: rowItem.completed,
        },
      };
    },
    renderNameCell(
      index: number,
      item: RowItem,
      header: CardHeader,
      rowItem: RowItem,
      prop: string,
    ) {
      return {
        component: NameCell,
        props: {
          name: rowItem[prop],
        },
      };
    },
    renderSupervisorName(
      index: number,
      item: RowItem,
      header: CardHeader,
      rowItem: RowItem,
    ) {
      return this.renderNameCell(index, item, header, rowItem, 'supervisor');
    },
    renderCreatorName(
      index: number,
      item: RowItem,
      header: CardHeader,
      rowItem: RowItem,
    ) {
      return this.renderNameCell(index, item, header, rowItem, 'creator');
    },
  },
};
</script>

<style scoped></style>

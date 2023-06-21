<template>
  <oxd-table-filter filter-title="Filter Assignments">
    <oxd-form @submit-valid="filterItems" @reset="filterItems">
      <oxd-form-row>
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field v-model="filters.name" label="Title" />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input v-model="filters.submittedAt" label="Submitted At" />
          </oxd-grid-item>
        </oxd-grid>

        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <employee-autocomplete
              v-model="filters.employee"
              label="Assignee"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <employee-autocomplete
              v-model="filters.supervisor"
              label="Supervisor"
            />
          </oxd-grid-item>
        </oxd-grid>

        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <date-input v-model="filters.startDate" label="Start Date" />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input v-model="filters.endDate" label="End Date" />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input v-model="filters.dueDate" label="Due Date" />
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-divider />

      <oxd-form-actions>
        <oxd-button
          display-type="ghost"
          :label="$t('general.reset')"
          type="reset"
        />
        <oxd-button
          class="orangehrm-left-space"
          display-type="secondary"
          :label="$t('general.search')"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </oxd-table-filter>

  <br />

  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <div class="orangehrm-header-container">
        <oxd-button
          label="Assign"
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
          :selectable="showActions"
          :loading="isLoading"
          class="orangehrm-employee-list"
          row-decorator="oxd-table-decorator-card"
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
import {
  OxdCardTable,
  OxdButton,
  OxdPagination,
  OxdForm,
  OxdDivider,
  OxdInputField,
  OxdTableFilter,
  OxdFormRow,
  OxdGrid,
  OxdGridItem,
  OxdFormActions,
} from '@ohrm/oxd';
import TableHeader from '@ohrm/components/table/TableHeader.vue';
import {CardHeader} from '@ohrm/oxd/types/components/CardTable/types';
import DeadlineDate from '@/onboardingPlugin/pages/task-groups/components/DeadlineDate.vue';
import CompleteCell from '@/onboardingPlugin/pages/task-groups/components/CompleteCell.vue';
import NameCell from '@/onboardingPlugin/pages/task-groups/components/NameCell.vue';
import {TaskGroup} from '@/onboardingPlugin/models';
import {navigate} from '@/core/util/helper/navigation';
import DateInput from '@/core/components/inputs/DateInput.vue';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete.vue';
import TaskProgress from './components/TaskProgress.vue';

const defaultSortOrder: SortDefinition = {
  'task.title': 'DEFAULT',
};

interface RowItem {
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  [key: string]: any;
  isSelectable?: boolean;
  isDisabled?: boolean;
}

type Filters = {
  name?: string;
  startDate?: string;
  endDate?: string;
  dueDate?: string;
  submittedAt?: string;
  completed?: boolean;
  employee?: {id: number} | null;
  supervisor?: {id: number} | null;
};

export default {
  name: 'TaskGroups',

  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    OxdCardTable,
    TableHeader,
    OxdPagination,
    OxdButton,
    OxdForm,
    OxdTableFilter,
    OxdFormRow,
    OxdGrid,
    OxdGridItem,
    OxdFormActions,
    OxdDivider,
    DateInput,
    OxdInputField,
    EmployeeAutocomplete,
  },

  props: {
    showActions: {
      type: Boolean,
      default: true,
    },
    apiUrl: {
      type: String,
      default: () => '/api/v2/task-management/task-assignments',
    },
    theme: {
      type: Object,
      required: true,
    },
  },

  setup(props: {apiUrl: string}) {
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const filters = ref<Filters>({
      name: '',
      startDate: '',
      endDate: '',
      dueDate: '',
      submittedAt: '',
      employee: null,
      supervisor: null,
    });

    const serializedFilters = computed(() => {
      return {
        name: filters.value.name,
        startDate: filters.value.startDate,
        endDate: filters.value.endDate,
        dueDate: filters.value.dueDate,
        employeeId: filters.value.employee?.id,
        supervisorId: filters.value.supervisor?.id,
        submittedAt: filters.value.submittedAt,
        sortField: sortField.value,
        sortOrder: sortOrder.value,
      };
    });

    const http = new APIService(
      (window as any).appGlobal.baseUrl,
      props.apiUrl,
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
    headers(): CardHeader[] {
      const headers = [
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
          name: 'assignee',
          slot: 'assignee',
          title: 'Assignee',
          style: {flex: 1},
          cellRenderer: this.renderAssigneeName,
        },
        {
          name: 'progress',
          slot: 'progress',
          title: 'Progress',
          style: {flex: 1},
          cellRenderer: this.renderProgressCell,
        },
      ];
      const newItem: CardHeader = {
        name: 'actions',
        slot: 'action',
        title: this.$t('general.actions'),
        style: {flex: 1},
        cellType: 'oxd-table-cell-actions',
        cellConfig: {
          view: {
            onClick: this.onClickView,
            props: {
              name: 'eye-fill',
            },
          },
        },
      };

      if (this.showActions) {
        newItem.cellConfig = {
          ...newItem.cellConfig,
          edit: {
            onClick: this.onClickEdit,
            props: {
              name: 'pencil-fill',
            },
          },
          delete: {
            onClick: this.onClickDelete,
            component: 'oxd-icon-button',
            props: {
              name: 'trash',
            },
          },
        };
      }
      headers.push(newItem);
      return headers;
    },
  },

  methods: {
    onClickAdd() {
      navigate('/taskManagement/createTask');
    },
    onClickEdit(item: TaskGroup) {
      navigate(`/taskManagement/viewTaskGroups/edit/${item.id}`);
    },
    onClickView(item: TaskGroup) {
      navigate(`/taskManagement/viewTaskGroups/${item.id}`);
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
    renderProgressCell(
      index: number,
      item: RowItem,
      header: CardHeader,
      rowItem: RowItem,
    ) {
      return {
        component: TaskProgress,
        props: {
          progress: rowItem?.progress,
          fillColor: this.theme['--oxd-primary-one-color'] ?? null,
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
    renderAssigneeName(
      index: number,
      item: RowItem,
      header: CardHeader,
      rowItem: RowItem,
    ) {
      return this.renderNameCell(index, item, header, rowItem, 'assignee');
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

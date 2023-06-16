<template>
  <oxd-table-filter filter-title="Task Types Information">
    <oxd-form @submit-valid="filterItems" @reset="filterItems">
      <oxd-form-row>
        <oxd-grid :cols="4" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <h1>Hello</h1>
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
          :label="$t('general.add')"
          icon-name="plus"
          display-type="secondary"
          @click="onClickAdd"
        />
      </div>
      <table-header
        :selected="checkedItems.length"
        :total="items.data?.length"
        :loading="isLoading"
        @delete="onClickDeleteSelected"
      ></table-header>
      <div class="orangehrm-container">
        <oxd-card-table
          ref="cardTable"
          v-model:selected="checkedItems"
          v-model:order="sortDefinition"
          :headers="headers"
          :items="items?.data"
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

<script>
import {computed, ref} from 'vue';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import useSort from '@ohrm/core/util/composable/useSort';
import TableHeader from '@/core/components/table/TableHeader';
import {
  OxdForm,
  OxdTableFilter,
  OxdFormRow,
  OxdGrid,
  OxdGridItem,
  OxdFormActions,
  OxdButton,
  OxdDivider,
  OxdCardTable,
  OxdPagination,
} from '@ohrm/oxd';

const defaultSortOrder = {
  'task.title': 'DEFAULT',
  'task.type': 'DEFAULT',
  'jobTitle.jobTitleName': 'DEFAULT',
};
export default {
  name: 'TaskTypes',
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
    OxdForm,
    OxdFormActions,
    OxdFormRow,
    OxdGrid,
    OxdGridItem,
    OxdDivider,
    OxdTableFilter,
    OxdButton,
    OxdCardTable,
    OxdPagination,
    TableHeader,
  },

  props: {
    unSelectableTaskIds: {
      type: Array,
      default: () => [],
    },
  },

  setup(props) {
    const dataNormalizer = (data) => {
      return data.map((item) => {
        const selectable = props.unSelectableTaskIds.findIndex(
          (id) => id === item.id,
        );
        return {
          ...item,
          isSelectable: selectable === -1,
        };
      });
    };

    const filters = ref({
      name: '',
    });

    const {sortDefinition, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilters = computed(() => {
      return {
        name: filters.value.name,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/task-management/task-types',
    );

    const {
      showPaginator,
      currentPage,
      total,
      pages,
      pageSize,
      response,
      isLoading,
      execQuery,
    } = usePaginate(http, {
      query: serializedFilters,
      normalizer: dataNormalizer,
    });

    onSort(execQuery);

    return {
      http,
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      pageSize,
      execQuery,
      items: response,
      filters,
      sortDefinition,
    };
  },
  data() {
    return {
      checkedItems: [],
      rules: {
        tasks: [],
      },
    };
  },
  computed: {
    headers() {
      return [
        {
          name: 'name',
          slot: 'name',
          title: 'Name',
          sortField: 'taskType.name',
          style: {flex: 1},
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
    onClickEdit(item) {
      if (item.id) {
        navigate('/taskManagement/saveTaskType/{id}', {id: item.id});
      }
    },
    onClickDelete(item) {
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems([item.id]);
        }
      });
    },
    async filterItems() {
      await this.execQuery();
    },
    onClickDeleteSelected() {
      const ids = [];
      this.checkedItems.forEach((index) => {
        ids.push(this.items?.data[index].id);
      });
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    deleteItems(items) {
      if (items instanceof Array) {
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
      }
    },
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    onClickAdd() {
      navigate('/taskManagement/saveTaskType');
    },
  },
};
</script>

<style scoped></style>

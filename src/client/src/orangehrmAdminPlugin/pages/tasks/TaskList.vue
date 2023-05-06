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
          :items="items?.data"
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
import usei18n from '@/core/util/composable/usei18n';

const defaultSortOrder = {
  'task.title': 'DEFAULT',
  'task.type': 'DEFAULT',
  'jobTitle.jobTitleName': 'DEFAULT',
};
export default {
  name: 'TaskList',
  components: {
    'delete-confirmation': DeleteConfirmationDialog,
  },
  props: {
    unSelectableTaskIds: {
      type: Array,
      default: () => [],
    },
  },
  setup(props) {
    const {$t} = usei18n();
    const dataNormalizer = data => {
      return data.map(item => {
        const selectable = props.unSelectableTaskIds.findIndex(
          id => id === item.id,
        );
        return {
          ...item,
          jobTitle: item.jobTitle?.isDeleted
            ? item.jobTitle.title + $t('general.deleted')
            : item.jobTitle?.title,
          isSelectable: selectable === -1,
        };
      });
    };

    const filters = ref({
      title: null,
      jobTitleId: null,
      type: null,
    });

    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });

    const serializedFilters = computed(() => {
      return {
        model: 'detailed',
        title: filters.value.task?.id,
        type: filters.value.task?.type,
        jobTitleId: filters.value.jobTitleId?.id,
      };
    });

    const http = new APIService(
      window.appGlobal.baseUrl,
      '/api/v2/onboarding/tasks',
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
  computed: {
    headers() {
      return [
        {
          name: 'title',
          slot: 'title',
          title: 'Title',
          sortField: 'task.title',
          style: {flex: 1},
        },
        {
          name: 'typeText',
          slot: 'typeText',
          title: 'Type',
          sortField: 'task.typeText',
          style: {flex: 1},
        },
        {
          name: 'jobTitle',
          title: this.$t('general.job_title'),
          sortField: 'jobTitle.jobTitleName',
          style: {flex: 1},
        },
        {
          name: 'notes',
          slot: 'notes',
          title: 'Notes',
          sortField: 'task.notes',
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
  data() {
    return {
      checkedItems: [],
      rules: {
        tasks: [],
      },
    };
  },

  methods: {
    onClickEdit() {
      console.log('edit');
    },
    onClickDeleteSelected() {
      const ids = this.checkedItems.map(index => {
        return this.items?.data[index].id;
      });
      this.$refs.deleteDialog.showDialog().then(confirmation => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    onClickAdd() {
      navigate('/admin/saveTask');
    },
  },
};
</script>

<style scoped></style>

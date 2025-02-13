import {APIService} from '@/core/util/services/api.service';
import {computed, ref} from 'vue';
import useSort from '@/core/util/composable/useSort';
import usePaginate from '@/core/util/composable/usePaginate';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog.vue';
import {
  OxdButton,
  OxdCardTable,
  OxdDivider,
  OxdForm,
  OxdFormActions,
  OxdFormRow,
  OxdGrid,
  OxdGridItem,
  OxdInputField,
  OxdPagination,
  OxdTableFilter,
} from '@ohrm/oxd';
import TableHeader from '@ohrm/components/table/TableHeader.vue';
import DeadlineDate from '@/onboardingPlugin/pages/task-groups/components/DeadlineDate.vue';
import CompleteCell from '@/onboardingPlugin/pages/task-groups/components/CompleteCell.vue';
import ApprovalCell from '@/onboardingPlugin/pages/task-groups/components/ApprovalCell.vue';
import NameCell from '@/onboardingPlugin/pages/task-groups/components/NameCell.vue';
import {navigate} from '@/core/util/helper/navigation';
import DateInput from '@/core/components/inputs/DateInput.vue';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete.vue';
import TaskProgress from './components/TaskProgress.vue';
import useAssignmentActions from '@/onboardingPlugin/util/composable/useAssignmentActions';
import AddCommentModal from '@/commentsPlugin/add/AddCommentModal.vue';
import {MODEL_TYPE_GROUP_ASSIGNMENT} from '@/commentsPlugin/util/composable/useComments';
const defaultSortOrder = {
  'task.title': 'DEFAULT',
};
const MESSAGE_DELETE =
  'The selected record will be permanently deleted. Are you sure you want to continue?';
const MESSAGE_APPROVE =
  'The selected assignment will be approved. Are you sure you want to continue?';
const MESSAGE_REJECT =
  'The selected assignment will be rejected. Are you sure you want to continue?';
export default {
  name: 'TaskGroups',
  components: {
    AddCommentModal,
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
    userId: {
      type: String,
      required: true,
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
  setup(props) {
    const {sortDefinition, sortField, sortOrder, onSort} = useSort({
      sortDefinition: defaultSortOrder,
    });
    const filters = ref({
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
    const http = new APIService(window.appGlobal.baseUrl, props.apiUrl);
    const {assignmentActions, performAssignmentAction} =
      useAssignmentActions(http);
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
      assignmentActions,
      pages,
      execQuery,
      performAssignmentAction,
      taskGroups: response,
      filters,
      sortDefinition,
    };
  },
  data() {
    return {
      checkedItems: [],
      showCommentModal: false,
      commentModalState: null,
      selectedTaskGroup: null,
      modelType: MODEL_TYPE_GROUP_ASSIGNMENT,
      confirmationMessage: MESSAGE_DELETE,
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
        {
          name: 'approvals',
          slot: 'approvals',
          title: '',
          style: {flex: 1},
          cellRenderer: this.renderApprovalCell,
        },
        {
          name: 'actions',
          slot: 'action',
          title: '',
          style: {flex: 1, flexDirection: 'row'},
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.actionCellRenderer,
        },
      ];
    },
  },
  methods: {
    actionCellRenderer(...[, , , row]) {
      const cellConfig = {};
      const {more} = this.assignmentActions;
      const dropdownActions = [
        {label: this.$t('general.add_comment'), context: 'add_comment'},
        {label: 'View Assignment Details', context: 'assignment_details'},
        {label: this.$t('leave.view_pim_info'), context: 'pim_details'},
      ];
      if (row.creator.id === this.userId) {
        dropdownActions.push({
          label: 'Edit Assignment Details',
          context: 'edit_details',
        });
        dropdownActions.push({
          label: 'Delete Assignment',
          context: 'delete_assignment',
        });
      }
      more.props.options = dropdownActions;
      more.props.onClick = ($event) =>
        this.onAssignmentDropdownAction($event, row);
      cellConfig.more = more;
      return {
        props: {
          header: {
            cellConfig,
          },
        },
      };
    },
    onAssignmentDropdownAction(event, row) {
      switch (event.context) {
        case 'delete_assignment':
          this.confirmationMessage = MESSAGE_DELETE;
          this.onClickDelete(row);
          break;
        case 'edit_details':
          this.onClickEdit(row);
          break;
        case 'pim_details':
          navigate('/pim/viewPersonalDetails/empNumber/{id}', {
            id: row.assignee.id.toString(),
          });
          break;
        case 'add_comment':
          this.selectedTaskGroup = row;
          this.showCommentModal = true;
          break;
        case 'assignment_details':
          this.onClickView(row);
          break;
      }
    },
    onClickAdd() {
      navigate('/taskManagement/createTask');
    },
    onClickEdit(item) {
      navigate(`/taskManagement/viewTaskGroups/edit/${item.id}`);
    },
    onClickView(item) {
      navigate(`/taskManagement/viewTaskGroups/${item.id}`);
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
    async resetDataTable() {
      this.checkedItems = [];
      await this.execQuery();
    },
    deleteItems(items) {
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
    onCommentModalClose() {
      this.commentModalState = null;
      this.showCommentModal = false;
      this.resetDataTable();
    },
    onClickDeleteSelected() {
      const ids = [];
      this.checkedItems.forEach((index) => {
        ids.push(this.taskGroups?.data[index].id);
      });
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.deleteItems(ids);
        }
      });
    },
    dueDateCellRenderer(index, item, header, rowItem) {
      return this.renderDateCell(index, item, header, rowItem, 'dueDate');
    },
    endDateCellRenderer(index, item, header, rowItem) {
      return this.renderDateCell(index, item, header, rowItem, 'endDate');
    },
    renderDateCell(index, item, header, rowItem, prop) {
      return {
        component: DeadlineDate,
        props: {
          date: rowItem[prop],
          isComplete: rowItem.completed,
        },
      };
    },
    renderCompletedCell(index, item, header, rowItem) {
      return {
        component: CompleteCell,
        props: {
          isComplete: rowItem.completed,
        },
      };
    },
    renderProgressCell(index, item, header, rowItem) {
      return {
        component: TaskProgress,
        props: {
          progress: rowItem?.progress,
          fillColor: this.theme['--oxd-primary-one-color'] ?? null,
        },
      };
    },
    renderNameCell(index, item, header, rowItem, prop) {
      return {
        component: NameCell,
        props: {
          name: rowItem[prop],
        },
      };
    },
    renderApprovalCell(index, item, header, rowItem) {
      return {
        component: ApprovalCell,
        props: {
          taskGroup: rowItem,
        },
      };
    },
    renderSupervisorName(index, item, header, rowItem) {
      return this.renderNameCell(index, item, header, rowItem, 'supervisor');
    },
    renderAssigneeName(index, item, header, rowItem) {
      return this.renderNameCell(index, item, header, rowItem, 'assignee');
    },
    renderCreatorName(index, item, header, rowItem) {
      return this.renderNameCell(index, item, header, rowItem, 'creator');
    },
  },
  mounted() {
    console.clear();
    console.log('this', this);
    this.emitter.on('approve', (taskGroup) => {
      this.confirmationMessage = MESSAGE_APPROVE;
      console.log('triggered', t);
      this.$refs.deleteDialog.showDialog().then((confirmation) => {
        if (confirmation === 'ok') {
          this.performAssignmentAction({
            action: 'approve',
            groupAssignmentId: taskGroup.id,
          });
        }
      });
    });
    this.emitter.on('reject', (t) => {
      this.confirmationMessage = MESSAGE_REJECT;
    });
  },
};
//# sourceMappingURL=task-groups.js.map

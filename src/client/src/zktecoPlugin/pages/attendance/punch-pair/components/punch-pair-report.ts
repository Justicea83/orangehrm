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
import DateInput from '@/core/components/inputs/DateInput.vue';
import EmployeeAutocomplete from '@/core/components/inputs/EmployeeAutocomplete.vue';
import {computed, ref, watch} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@/core/util/composable/usePaginate';
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown.vue';
import SubunitDropdown from '@/orangehrmPimPlugin/components/SubunitDropdown.vue';
import ReportModeDropdown from '@/zktecoPlugin/pages/attendance/components/ReportModeDropdown.vue';
import {format} from 'date-fns';
import {CardHeader} from '@ohrm/oxd/types/components/CardTable/types';
import SubunitAutocomplete from '@/zktecoPlugin/pages/attendance/components/SubunitAutocomplete.vue';
import ExportColumnPickerModal from '@/zktecoPlugin/pages/attendance/components/ExportColumnPickerModal.vue';
import SyncZkTeco from '@/zktecoPlugin/pages/configure/connection/components/SyncZkTeco.vue';

type Filters = {
  date?: string;
  employees?: {id: number}[] | null;
  departments?: {id: number}[] | null;
  jobTitles?: {id: number}[] | null;
  reportMode?: {id: string; label: string} | null;
};

const REPORT_MODE_DAILY = 'daily';

export default {
  name: 'PunchPairReport',
  components: {
    SyncZkTeco,
    JobtitleDropdown,
    ReportModeDropdown,
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
    SubunitDropdown,
    SubunitAutocomplete,
    ExportColumnPickerModal,
  },
  data() {
    return {
      showExportModal: false,
      exportType: 'csv',
    };
  },
  methods: {
    async filterItems() {
      await this.execQuery();
    },
    onClickExportCsv() {
      this.showExportModal = true;
      this.exportType = 'csv';
    },
    onExport(columns: string[]) {
      this.showExportModal = false;
      this.exportCsv(columns);
    },
    exportCsv(columns: string[] = []) {
      const baseUrl = `${
        (window as any).appGlobal.baseUrl
      }/zkteco/exports/punchPair`;

      const filters = {
        ...this.serializedFilters,
        columns: columns.join(','),
        type: this.exportType,
      };

      const filteredParams: Record<string, string> = Object.fromEntries(
        Object.entries(filters)
          .filter(([_, v]) => v !== undefined && v !== null)
          .map(([k, v]) => [k, String(v)]), // Ensure all values are strings
      );

      const params = new URLSearchParams(filteredParams).toString();
      const downUrl = `${baseUrl}?${params}`;
      window.open(downUrl, '_blank');
    },
  },
  props: {
    apiUrl: {
      type: String,
      default: () => '/api/v2/zkteco/attendance/punch-pair-report',
    },
  },
  setup(props: {apiUrl: string}) {
    const filters = ref<Filters>({
      reportMode: {
        id: REPORT_MODE_DAILY,
        label: 'Daily',
      },
      date: format(new Date(Date.now() - 86400000), 'yyyy-MM-dd'),
    });
    const showReportDate = ref<boolean>(true);
    const columns = ref<string[]>([]);

    const serializedFilters = computed(() => {
      return {
        employees: filters.value.employees?.map((emp) => emp.id).join(','),
        date: filters.value.date,
        departments: filters.value.departments?.map((dep) => dep.id).join(','),
        reportMode: filters.value.reportMode?.id,
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

    watch(
      filters,
      (val) => {
        showReportDate.value = val.reportMode?.id === REPORT_MODE_DAILY;
      },
      {deep: true},
    );

    watch(
      response,
      (val) => {
        if (val?.data?.length > 0) {
          columns.value = Object.keys(val.data[0]);
        } else {
          columns.value = [];
        }
      },
      {deep: true},
    );

    return {
      http,
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      filters,
      reportData: response,
      serializedFilters,
      execQuery,
      columns,
      showReportDate,
    };
  },
  computed: {
    headers(): CardHeader[] {
      return [
        {
          name: 'first_name',
          slot: 'first_name',
          title: 'First Name',
          style: {flex: 1},
        },
        {
          name: 'last_name',
          slot: 'last_name',
          title: 'Last Name',
          style: {flex: 1},
        },
        {
          name: 'nick_name',
          slot: 'nick_name',
          title: 'Nick Name',
          style: {flex: 1},
        },
        {
          name: 'dept_name',
          slot: 'dept_name',
          title: 'Department',
          style: {flex: 1},
        },
        {
          name: 'att_date',
          slot: 'att_date',
          title: 'Date',
          style: {flex: 1},
        },
        {
          name: 'weekday',
          slot: 'weekday',
          title: 'Week Day',
          sortField: 't.dueDate',
          style: {flex: 1},
        },
        {
          name: 'check_in',
          slot: 'check_in',
          title: 'Check In',
          style: {flex: 1},
        },
        {
          name: 'check_out',
          slot: 'check_out',
          title: 'Check Out',
          style: {flex: 1},
        },
        {
          name: 'total_time',
          slot: 'total_time',
          title: 'Total Time',
          style: {flex: 1},
        },
        {
          name: 'hourly_rate',
          slot: 'hourly_rate',
          title: 'Hourly Rate',
          style: {flex: 1},
        },
        {
          name: 'total_comp',
          slot: 'total_comp',
          title: 'Total Comp',
          style: {flex: 1},
        },
        {
          name: 'currency',
          slot: 'currency',
          title: 'Currency',
          style: {flex: 1},
        },
        {
          name: 'position_name',
          slot: 'position_name',
          title: 'Position',
          style: {flex: 1},
        },
      ];
    },
  },
};

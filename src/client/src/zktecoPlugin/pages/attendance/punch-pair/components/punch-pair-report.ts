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
import JobtitleDropdown from '@/orangehrmPimPlugin/components/JobtitleDropdown';
import SubunitDropdown from '@/orangehrmPimPlugin/components/SubunitDropdown';
import ReportModeDropdown from '@/zktecoPlugin/pages/attendance/components/ReportModeDropdown.vue';
import {format} from 'date-fns';

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
  },
  methods: {
    async filterItems() {
      console.log(JSON.parse(JSON.stringify(this.filters)));
      //await this.execQuery();
    },
  },
  props: {
    apiUrl: {
      type: String,
      default: () => '/api/v2/task-management/task-assignments',
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

    const serializedFilters = computed(() => {
      return {
        employees: filters.value.employees?.map(emp => emp.id),
        date: filters.value.date,
        departments: filters.value.departments?.map(dep => dep.id),
        jobTitles: filters.value.jobTitles?.map(job => job.id),
        reportMode: filters.value.reportMode?.id,
      };
    });

    console.log('sea', serializedFilters)

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

    return {
      http,
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      filters,
      reportData: response,
      execQuery,
      showReportDate,
    };
  },
};

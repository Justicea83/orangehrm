<template>
  <oxd-table-filter filter-title="Punch Pair Report">
    <template #exportOptions>
      <oxd-button
        size="small"
        display-type="tool"
        label="CSV"
        icon-name="file-earmark-spreadsheet"
        @click="onClickExportCsv()"
      />
    </template>
    <oxd-form @submit-valid="filterItems">
      <oxd-form-row>
        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <employee-autocomplete
              v-model="filters.employees"
              multiple
              label="Employees"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <subunit-autocomplete v-model="filters.departments" multiple />
          </oxd-grid-item>
        </oxd-grid>

        <oxd-grid :cols="2" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <report-mode-dropdown v-model="filters.reportMode" />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input v-model="filters.date" label="Date" />
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
          label="Filter"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </oxd-table-filter>

  <br />

  <export-column-picker-modal
    v-if="showExportModal"
    :columns="headers"
    @on-export="onExport"
    @close="showExportModal = false"
  />

  <div class="orangehrm-background-container">
    <div class="orangehrm-paper-container">
      <table-header :total="total" :loading="isLoading" />
      <div class="orangehrm-container">
        <oxd-card-table
          ref="cardTable"
          :headers="headers"
          :items="reportData?.data"
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
  </div>
</template>

<script lang="ts" src="./punch-pair-report.ts"></script>

<style scoped lang="scss"></style>

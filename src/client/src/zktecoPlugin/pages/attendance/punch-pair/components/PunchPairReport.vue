<template>
  <oxd-table-filter filter-title="Generate Punch Pair Report">
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
            <subunit-dropdown v-model="filters.departments" multiple />
          </oxd-grid-item>
        </oxd-grid>

        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <report-mode-dropdown v-model="filters.reportMode" />
          </oxd-grid-item>
          <oxd-grid-item>
            <date-input v-model="filters.date" label="Date" />
          </oxd-grid-item>
          <oxd-grid-item>
            <jobtitle-dropdown v-model="filters.jobTitles" multiple />
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
          label="Generate"
          type="submit"
        />
      </oxd-form-actions>
    </oxd-form>
  </oxd-table-filter>
</template>

<script lang="ts" src="./punch-pair-report.ts"></script>

<style scoped lang="scss"></style>

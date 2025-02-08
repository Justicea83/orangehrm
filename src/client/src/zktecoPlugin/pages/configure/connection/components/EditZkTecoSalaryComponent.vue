<template>
  <div class="orangehrm-horizontal-padding orangehrm-vertical-padding">
    <oxd-text tag="h6" class="orangehrm-main-title">
      Update Salary Component
    </oxd-text>
    <oxd-divider />
    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-grid :cols="3" class="orangehrm-full-width-grid">
          <oxd-grid-item>
            <oxd-input-field
              v-model="salaryComponent.name"
              :label="$t('pim.salary_component')"
              :rules="rules.name"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="salaryComponent.payGradeId"
              type="select"
              :label="$t('general.pay_grade')"
              :options="paygrades"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="salaryComponent.payFrequencyId"
              type="select"
              :label="$t('pim.pay_frequency')"
              :options="payFrequencies"
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              :key="currenciesOpts"
              v-model="salaryComponent.currencyId"
              type="select"
              :label="$t('general.currency')"
              :options="currenciesOpts"
              :rules="rules.currencyId"
              required
            />
          </oxd-grid-item>
          <oxd-grid-item>
            <oxd-input-field
              v-model="salaryComponent.salaryAmount"
              :label="$t('pim.amount')"
              :rules="rules.salaryAmount"
              required
            />
            <oxd-text
              v-if="minAmount !== undefined || maxAmount !== undefined"
              class="orangehrm-input-hint"
              tag="p"
            >
              Min: {{ minAmount ?? 0 }} - Max: {{ maxAmount ?? 0 }}
            </oxd-text>
          </oxd-grid-item>
        </oxd-grid>
      </oxd-form-row>

      <oxd-form-actions>
        <required-text />
        <oxd-button
          type="button"
          display-type="ghost"
          :label="$t('general.cancel')"
          @click="onCancel"
        />
        <submit-button />
      </oxd-form-actions>
    </oxd-form>
  </div>
  <oxd-divider />
</template>

<script>
import {
  digitsOnlyWithDecimalPoint,
  maxCurrency,
  required,
  shouldNotExceedCharLength,
} from '@ohrm/core/util/validation/rules';
import {reloadPage} from '@/core/util/helper/navigation';

const salComponentModel = {
  name: '',
  salaryAmount: '',
  payGradeId: null,
  payFrequencyId: null,
  currencyId: null,
};

export default {
  name: 'EditZkTecoSalaryComponent',

  props: {
    http: {
      type: Object,
      required: true,
    },
    data: {
      type: Object,
      required: true,
    },
    paygrades: {
      type: Array,
      default: () => [],
    },
    payFrequencies: {
      type: Array,
      default: () => [],
    },
    currencies: {
      type: Array,
      default: () => [],
    },
  },

  emits: ['close'],

  data() {
    return {
      isLoading: false,
      includeDirectDeposit: false,
      salaryComponent: {...salComponentModel},
      accountType: '',
      usableCurrencies: [],
      rules: {
        name: [required, shouldNotExceedCharLength(100)],
        salaryAmount: [
          required,
          digitsOnlyWithDecimalPoint,
          maxCurrency(1000000000),
        ],
        currencyId: [required],
      },
    };
  },

  computed: {
    minAmount() {
      return this.currencyInfo?.minAmount;
    },
    maxAmount() {
      return this.currencyInfo?.maxAmount;
    },
    currenciesOpts() {
      const paygrade = this.salaryComponent.payGradeId?.id;
      if (!paygrade) {
        return this.currencies;
      } else if (paygrade && this.usableCurrencies.length > 0) {
        return this.currencies.filter(
          (item) =>
            this.usableCurrencies.findIndex(
              (currency) => currency.id === item.id,
            ) > -1,
        );
      } else {
        return [];
      }
    },
    currencyInfo() {
      return this.usableCurrencies.find(
        (item) => item.id === this.salaryComponent.currencyId?.id,
      );
    },
  },

  watch: {
    'salaryComponent.payGradeId': function (newVal) {
      if (newVal?.id) {
        this.isLoading = true;
        this.http
          .request({
            url: `/api/v2/admin/pay-grades/${newVal.id}/currencies`,
            method: 'GET',
            params: {limit: 0},
          })
          .then((response) => {
            const {data} = response.data;
            this.usableCurrencies = data.map((item) => {
              return {
                id: item.currencyType.id,
                name: item.currencyType.name,
                minAmount: item.minSalary,
                maxAmount: item.maxSalary,
              };
            });
            const currency = this.salaryComponent.currencyId;
            const currencyIndex = this.usableCurrencies.findIndex(
              (item) => item.id === currency?.id,
            );
            this.salaryComponent.currencyId =
              currencyIndex === -1 ? null : this.salaryComponent.currencyId;
          })
          .then(() => {
            this.isLoading = false;
          });
      } else {
        this.usableCurrencies = [];
      }
    },
  },

  beforeMount() {
    console.clear();
    console.log(JSON.parse(JSON.stringify(this.currencies)));
    this.salaryComponent.name = this.data.salaryComponent;
    this.salaryComponent.salaryAmount = this.data.amount;
    this.salaryComponent.payGradeId = this.data.payGradeId
      ? this.paygrades.find(
          (item) => parseInt(item.id) === parseInt(this.data.payGradeId),
        )
      : null;
    this.salaryComponent.payFrequencyId = this.data.payFrequencyId
      ? this.payFrequencies.find(
          (item) => parseInt(item.id) === parseInt(this.data.payFrequencyId),
        )
      : null;
    this.salaryComponent.currencyId = this.data.currencyId
      ? this.currencies.find((item) => item.id === this.data.currencyId)
      : null;
  },

  mounted() {
    this.$nextTick(() => {
      this.rules.salaryAmount.push((v) => {
        const min = this.minAmount ? this.minAmount : 0;
        return v >= min || this.$t('pim.should_be_within_min_max_values');
      });
      this.rules.salaryAmount.push((v) => {
        const max = this.maxAmount ? this.maxAmount : 999999999;
        return v <= max || this.$t('pim.should_be_within_min_max_values');
      });
    });
  },

  methods: {
    onSave() {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          url: `/api/v2/zkteco/salary-components/${this.data.id}`,
          data: {
            salaryComponent: this.salaryComponent.name,
            salaryAmount: this.salaryComponent.salaryAmount,
            payGradeId: this.salaryComponent.payGradeId?.id,
            currencyId: this.salaryComponent.currencyId?.id,
            payFrequencyId: this.salaryComponent.payFrequencyId?.id,
            id: this.data.id,
          },
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .finally(() => {
          reloadPage();
        });
    },
    onCancel() {
      this.$emit('close', true);
    },
  },
};
</script>

<style lang="scss" scoped>
.directdeposit-form-header {
  display: flex;
  padding: 1rem;
  &-text {
    font-size: 0.8rem;
    margin-right: 1rem;
  }
}
</style>

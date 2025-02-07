import {nanoid} from 'nanoid';
import {OxdToast, TOAST_TYPES} from '@ohrm/oxd';
import {translate as translatorFactory} from '@/core/plugins/i18n/translate';
import {h, defineComponent, TransitionGroup, reactive, toRefs} from 'vue';
const translate = translatorFactory();
const state = reactive({
  toasts: [],
  transition: '',
  class: '',
  position: '',
});
const Toaster = defineComponent({
  name: 'OxdToaster',
  setup() {
    return {
      ...toRefs(state),
    };
  },
  computed: {
    classes() {
      return {
        'oxd-toast-container': true,
        [`oxd-toast-container--${this.position}`]: true,
      };
    },
  },
  methods: {
    onUpdateShow(state, index) {
      if (state === false) {
        this.toasts.splice(index, 1);
      }
    },
  },
  render() {
    return h(
      TransitionGroup,
      {appear: true, name: this.transition, tag: 'div', class: this.classes},
      {
        default: () =>
          this.toasts.map((toast, index) => {
            return h(OxdToast, {
              key: toast.id,
              type: toast.type,
              title: toast.title,
              message: toast.message,
              show: toast.show,
              class: this.class,
              'onUpdate:show': (state) => this.onUpdateShow(state, index),
            });
          }),
      },
    );
  },
});
export default {
  install: (app, options) => {
    // Create toaster vdom element
    const toastWrapper = document.createElement('oxd-toaster');
    toastWrapper.id = 'oxd-toaster_1';
    document.getElementById('app').appendChild(toastWrapper);
    // Toaster API
    const clear = (id) => {
      if (typeof id === 'string') {
        const _index = state.toasts.findIndex((item) => item.id === id);
        if (_index > -1) {
          clear(_index);
        }
      } else if (state.toasts[id]) {
        state.toasts.splice(id, 1);
      }
    };
    const notify = (toast) => {
      return new Promise((resolve) => {
        const _id = nanoid(8);
        state.toasts.push({...toast, id: _id});
        if (!options.persist) {
          const _duration = options.duration ? options.duration : 2500;
          setTimeout(() => {
            clear(_id);
            resolve(_id);
          }, _duration);
        } else {
          resolve(_id);
        }
      });
    };
    const success = (message) => {
      return notify({
        id: '',
        type: TOAST_TYPES.TYPE_SUCCESS,
        show: true,
        ...message,
      });
    };
    const error = (message) => {
      return notify({
        id: '',
        type: TOAST_TYPES.TYPE_ERROR,
        show: true,
        ...message,
      });
    };
    const info = (message) => {
      return notify({
        id: '',
        type: TOAST_TYPES.TYPE_INFO,
        show: true,
        ...message,
      });
    };
    const warn = (message) => {
      return notify({
        id: '',
        type: TOAST_TYPES.TYPE_WARN,
        show: true,
        ...message,
      });
    };
    const show = (message) => {
      return notify({
        id: '',
        type: TOAST_TYPES.TYPE_DEFAULT,
        show: true,
        ...message,
      });
    };
    const clearAll = () => {
      state.toasts = [];
    };
    const saveSuccess = () =>
      success({
        title: translate('general.success'),
        message: translate('general.successfully_saved'),
      });
    const generalSuccess = (message) =>
      success({
        title: translate('general.success'),
        message: message,
      });
    const addSuccess = () =>
      success({
        title: translate('general.success'),
        message: translate('general.successfully_added'),
      });
    const updateSuccess = () =>
      success({
        title: translate('general.success'),
        message: translate('general.successfully_updated'),
      });
    const deleteSuccess = () =>
      success({
        title: translate('general.success'),
        message: translate('general.successfully_deleted'),
      });
    const cannotDelete = () =>
      error({
        title: translate('general.error'),
        message: translate('general.cannot_be_deleted'),
      });
    const noRecordsFound = () =>
      info({
        title: translate('general.info'),
        message: translate('general.no_records_found'),
      });
    const unexpectedError = (errorMessage) =>
      error({
        title: translate('general.error'),
        message: errorMessage ?? translate('general.unexpected_error'),
      });
    state.class = options.class ? options.class : 'oxd-toast-container--toast';
    state.transition = options.animation ? options.animation : 'oxd-toast-list';
    state.position = options.position ? options.position : 'bottom';
    // Define Toaster component
    app.component('OxdToaster', Toaster);
    // Add Toaster API to Vue global scope
    const toasterAPI = {
      notify,
      show,
      success,
      error,
      info,
      warn,
      clear,
      clearAll,
      saveSuccess,
      addSuccess,
      updateSuccess,
      deleteSuccess,
      generalSuccess,
      cannotDelete,
      noRecordsFound,
      unexpectedError,
    };
    app.config.globalProperties.$toast = toasterAPI;
  },
};
//# sourceMappingURL=toaster.js.map

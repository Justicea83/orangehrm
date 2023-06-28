import { h, defineComponent, Transition, reactive, toRefs } from 'vue';
import { OxdOverlay, OxdSpinner } from '@ohrm/oxd';
const state = reactive({
    show: false,
});
const Loader = defineComponent({
    name: 'OxdLoader',
    setup() {
        return {
            ...toRefs(state),
        };
    },
    render() {
        return h(Transition, { name: 'orangehrm-loader-fade', tag: 'div' }, {
            default: () => {
                if (this.show) {
                    return h(OxdOverlay, { show: true, centered: true, class: 'orangehrm-loader' }, h(OxdSpinner, { withContainer: false }));
                }
            },
        });
    },
});
export default {
    install: (app) => {
        // Create loader vdom element
        const loaderWrapper = document.createElement('oxd-loader');
        loaderWrapper.id = 'oxd-loader_1';
        document.getElementById('app').appendChild(loaderWrapper);
        // loader API
        const startLoading = () => {
            state.show = true;
        };
        const endLoading = () => {
            state.show = false;
        };
        // Define Toaster component
        app.component('OxdLoader', Loader);
        // Add Toaster API to Vue global scope
        const loaderAPI = {
            startLoading,
            endLoading,
        };
        app.config.globalProperties.$loader = loaderAPI;
    },
};
//# sourceMappingURL=loader.js.map
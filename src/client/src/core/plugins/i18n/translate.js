/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
import IntlMessageFormat from 'intl-messageformat';
import { APIService } from '@/core/util/services/api.service';
import { StoreService } from '@ohrm/oxd';
export const langStrings = {};
/**
 * A factory function that will return translator function
 * @return {function(key, parameters): string}
 */
export const translate = () => (key, 
// eslint-disable-next-line @typescript-eslint/no-explicit-any
parameters = {}) => {
    // IntlMessageFormat.format method will throw error if not every argument in the message pattern
    // has been provided. sourrounded by try catch to fallback incase of param resolution
    try {
        if (!langStrings[key])
            return key;
        const translatedString = langStrings[key].format(parameters);
        if (Array.isArray(translatedString)) {
            return typeof translatedString[0] === 'string'
                ? translatedString[0]
                : key;
        }
        return translatedString;
    }
    catch (error) {
        // eslint-disable-next-line no-console
        console.error(error);
        return key;
    }
};
const defineMixin = () => {
    return {
        beforeCreate() {
            this.$t = translate();
        },
    };
};
function createI18n(options) {
    const http = new APIService(options.baseUrl, options.resourceUrl);
    return {
        init: function () {
            return new Promise((resolve) => {
                http
                    .request({
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                        contentType: 'application/json',
                        ...(process.env.NODE_ENV === 'development' && {
                            'Cache-Control': 'public,  max-age=60',
                        }),
                    },
                })
                    .then((response) => {
                    const { data } = response;
                    const language = {};
                    for (const key in data) {
                        // https://formatjs.io/docs/intl-messageformat#intlmessageformat-constructor
                        language[key] = data[key].target || data[key].source;
                        langStrings[key] = new IntlMessageFormat(data[key].target || data[key].source, undefined, undefined, { ignoreTag: true });
                    }
                    StoreService.mergeConfig({
                        language,
                    });
                })
                    .finally(() => resolve());
            });
        },
        i18n: function (app) {
            app.mixin(defineMixin());
        },
    };
}
export default createI18n;
//# sourceMappingURL=translate.js.map
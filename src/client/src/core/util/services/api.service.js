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
import axios from 'axios';
import {WebStorage} from '../helper/storage';
import {getCurrentInstance} from 'vue';
import {reloadPage} from '@ohrm/core/util/helper/navigation';
export class APIService {
  _http;
  _baseUrl;
  _apiSection;
  _cacheStorage;
  _ignorePathRegex;
  constructor(baseUrl, path = '') {
    this._baseUrl = baseUrl;
    this._apiSection = path;
    this._http = axios.create({
      baseURL: this._baseUrl,
    });
    this._cacheStorage = new WebStorage(localStorage);
    this.setupResponseInterceptors(getCurrentInstance());
  }
  setIgnorePath(ignorePath) {
    this._ignorePathRegex = new RegExp(ignorePath);
  }
  getAll(params) {
    const headers = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'Cache-Control':
        'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
    };
    return this._http.get(this._apiSection, {headers, params});
  }
  get(id, params) {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.get(`${this._apiSection}/${id}`, {headers, params});
  }
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  create(data) {
    const headers = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    };
    return this._http.post(this._apiSection, data, {headers});
  }
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  update(id, data) {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.put(`${this._apiSection}/${id}`, data, {headers});
  }
  delete(id) {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.delete(`${this._apiSection}/${id}`, {headers});
  }
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  deleteAll(data) {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.delete(`${this._apiSection}`, {headers, data});
  }
  request(options) {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.request({
      url: this._apiSection,
      headers,
      ...options,
    });
  }
  // Function to prevent Error toast messages from showing
  ignoreError(error) {
    if (
      this._ignorePathRegex &&
      (error.response?.status === 422 || error.response?.status === 400)
    ) {
      const url = error.response.config.url ?? '';
      return this._ignorePathRegex.test(url);
    }
    return false;
  }
  /**
   * ComponentInternalInstance is given to access $toast api.
   * will fail silently if $toast is not installed/NA
   */
  setupResponseInterceptors(vm) {
    this._http.interceptors.response.use(
      (response) => {
        return response;
      },
      (error) => {
        if (error.response?.status === 401) {
          reloadPage();
          return Promise.reject();
        }
        if (this.ignoreError(error)) {
          return Promise.reject(error.response);
        }
        const $toast = vm?.appContext.config.globalProperties.$toast;
        if ($toast && error.code !== 'ECONNABORTED') {
          const response = error.response?.data;
          $toast.unexpectedError(response?.error.message || null);
        }
        return Promise.reject(error);
      },
    );
    if (process.env.NODE_ENV !== 'development') {
      const removeETagWeakValidatorDirective = (etag) => {
        return etag.startsWith('W/') ? etag.substring(2) : etag;
      };
      // Additional interceptors for caching
      this._http.interceptors.request.use(
        // eslint-disable-next-line @typescript-eslint/ban-ts-comment
        // @ts-ignore
        (config) => {
          if (config.url) {
            const url = config.url;
            const cachedEtag = this._cacheStorage.getItem(url);
            if (cachedEtag) {
              config.headers = {
                ...config.headers,
                // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/If-None-Match
                'If-None-Match': cachedEtag,
              };
            }
          }
          return config;
        },
        (error) => {
          return Promise.reject(error);
        },
      );
      this._http.interceptors.response.use(
        (response) => {
          const {config, headers} = response;
          if (config.url && headers) {
            const url = config.url;
            const etag = headers['etag'];
            const cachedEtag = this._cacheStorage.getItem(url);
            if (etag && etag !== cachedEtag) {
              this._cacheStorage.removeItem(url);
              this._cacheStorage.setItem(
                url,
                removeETagWeakValidatorDirective(etag),
              );
              if (cachedEtag) this._cacheStorage.removeItem(cachedEtag);
              this._cacheStorage.setItem(
                removeETagWeakValidatorDirective(etag),
                JSON.stringify(response.data),
              );
            }
          }
          return response;
        },
        (error) => {
          if (error.response?.status === 304) {
            const etag = error.response.headers['etag'];
            if (etag) {
              const cacheData = this._cacheStorage.getItem(
                removeETagWeakValidatorDirective(etag),
              );
              if (cacheData) {
                return Promise.resolve({
                  ...error.response,
                  status: 200,
                  data: JSON.parse(cacheData),
                });
              }
            }
          }
          return Promise.reject(error);
        },
      );
    }
  }
  get http() {
    return this._http;
  }
  get baseUrl() {
    return this._baseUrl;
  }
  set apiSection(path) {
    this._apiSection = path;
  }
}
//# sourceMappingURL=api.service.js.map

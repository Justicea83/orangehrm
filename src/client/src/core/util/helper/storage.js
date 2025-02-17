/*
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
class TempStorage {
  _tempStorage = {};
  clear() {
    this._tempStorage = {};
  }
  getItem(name) {
    return this._tempStorage[name] || null;
  }
  key(index) {
    return Object.keys(this._tempStorage)[index] || null;
  }
  removeItem(name) {
    delete this._tempStorage[name];
  }
  setItem(name, value) {
    this._tempStorage[name] = value;
  }
}
/**
 * Check storage API available
 * https://developer.mozilla.org/en-US/docs/Web/API/Web_Storage_API/Using_the_Web_Storage_API#testing_for_availability
 */
function isSupported(storage) {
  try {
    const x = '__storage_test__';
    storage.setItem(x, x);
    storage.removeItem(x);
    return true;
  } catch (e) {
    return (
      e instanceof DOMException &&
      // everything except Firefox
      (e.code === 22 ||
        // Firefox
        e.code === 1014 ||
        // test name field too, because code might not be present
        // everything except Firefox
        e.name === 'QuotaExceededError' ||
        // Firefox
        e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
      // acknowledge QuotaExceededError only if there's something already stored
      storage &&
      storage.length !== 0
    );
  }
}
export class WebStorage {
  _storage;
  constructor(storage) {
    if (isSupported(storage)) {
      this._storage = storage;
    } else {
      this._storage = new TempStorage();
    }
  }
  clear() {
    this._storage.clear();
  }
  getItem(name) {
    return this._storage.getItem(name);
  }
  key(index) {
    return this._storage.key(index);
  }
  removeItem(name) {
    this._storage.removeItem(name);
  }
  setItem(name, value) {
    this._storage.setItem(name, value);
  }
}
//# sourceMappingURL=storage.js.map

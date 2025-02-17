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
import {formatDate, getStandardTimezone} from '../datefns';
describe('core/util/helper/datefns', () => {
  test('format::HH:mm', () => {
    const result = formatDate(new Date('2021-12-24 10:40'), 'HH:mm');
    expect(result).toBe('10:40');
  });
  test('format::HH.mm', () => {
    const result = formatDate(new Date('2021-12-24 10:40'), 'HH.mm');
    expect(result).toBe('10.40');
  });
  test('format::YY-MM-DD HH.mm', () => {
    const result = formatDate(new Date('2021-12-24 10.40'), 'HH.mm');
    expect(result).toBe(null);
  });
  test('getStandardTimezone:+05:00', () => {
    const result = getStandardTimezone(5);
    expect(result).toBe('+05:00');
  });
  test('getStandardTimezone:+05:30', () => {
    const result = getStandardTimezone(5.5);
    expect(result).toBe('+05:30');
  });
  test('getStandardTimezone:-05:00', () => {
    const result = getStandardTimezone(-5);
    expect(result).toBe('-05:00');
  });
  test('getStandardTimezone:-05:30', () => {
    const result = getStandardTimezone(-5.5);
    expect(result).toBe('-05:30');
  });
});
//# sourceMappingURL=datefns.spec.js.map

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
import { reactive } from 'vue';
export default function useTimesheetAPIs(http) {
    const state = reactive({
        isLoading: false,
        employee: null,
        timesheet: null,
        timesheetId: null,
        timesheetRecords: [],
        timesheetStatus: null,
        timesheetColumns: null,
        timesheetSubtotal: null,
        timesheetAllowedActions: [],
        date: null,
    });
    const fetchTimesheet = (date, empNumber) => {
        return http.request({
            method: 'GET',
            url: '/api/v2/time/timesheets/default',
            params: {
                date,
                empNumber,
            },
        });
    };
    const updateTimesheet = (timesheetId, action, comment, empNumber) => {
        return http.request({
            method: 'PUT',
            url: empNumber
                ? `/api/v2/time/employees/${empNumber}/timesheets/${timesheetId}`
                : `/api/v2/time/timesheets/${timesheetId}`,
            data: {
                action,
                comment: comment ? comment : undefined,
            },
        });
    };
    const fetchTimesheetEntries = (timesheetId, isEmployeeTimesheet) => {
        return new Promise((resolve) => {
            http
                .request({
                method: 'GET',
                url: isEmployeeTimesheet
                    ? `/api/v2/time/employees/timesheets/${timesheetId}/entries`
                    : `/api/v2/time/timesheets/${timesheetId}/entries`,
            })
                .then((response) => {
                const { data, meta } = response.data;
                const { timesheet, allowedActions } = meta;
                resolve({ data, meta, timesheet, allowedActions });
            });
        });
    };
    const updateTimesheetEntries = (timesheetId, payload, isEmployeeTimesheet) => {
        return http.request({
            method: 'PUT',
            url: isEmployeeTimesheet
                ? `/api/v2/time/employees/timesheets/${timesheetId}/entries`
                : `/api/v2/time/timesheets/${timesheetId}/entries`,
            data: {
                ...payload,
            },
        });
    };
    return {
        state,
        fetchTimesheet,
        updateTimesheet,
        fetchTimesheetEntries,
        updateTimesheetEntries,
    };
}
//# sourceMappingURL=useTimesheetAPIs.js.map
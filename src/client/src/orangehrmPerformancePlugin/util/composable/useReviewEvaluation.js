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
import { lessThanOrEqual, greaterThanOrEqual, } from '@/core/util/validation/rules';
import usei18n from '@/core/util/composable/usei18n';
export default function useReviewEvaluation(http) {
    const { $t } = usei18n();
    const getAllKpis = (reviewId) => {
        return http.request({
            method: 'GET',
            url: `/api/v2/performance/reviews/${reviewId}/kpis`,
        });
    };
    const getSupervisorReview = (reviewId) => {
        return http.request({
            method: 'GET',
            url: `/api/v2/performance/reviews/${reviewId}/evaluation/supervisor`,
        });
    };
    const getEmployeeReview = (reviewId) => {
        return http.request({
            method: 'GET',
            url: `/api/v2/performance/reviews/${reviewId}/evaluation/employee`,
        });
    };
    const getFinalReview = (reviewId) => {
        return http.request({
            method: 'GET',
            url: `/api/v2/performance/reviews/${reviewId}/evaluation/final`,
        });
    };
    const finalizeReview = (reviewId, reviewData) => {
        return http.request({
            method: 'PUT',
            url: `/api/v2/performance/reviews/${reviewId}/evaluation/final`,
            data: {
                ...reviewData,
                finalComment: reviewData.finalComment === '' ? null : reviewData.finalComment,
            },
        });
    };
    const saveEmployeeReview = (reviewId, complete, review) => {
        return http.request({
            method: 'PUT',
            url: `/api/v2/performance/reviews/${reviewId}/evaluation/employee`,
            data: {
                complete,
                ratings: review.kpis,
                generalComment: review.generalComment,
            },
        });
    };
    const saveSupervisorReview = (reviewId, review) => {
        return http.request({
            method: 'PUT',
            url: `/api/v2/performance/reviews/${reviewId}/evaluation/supervisor`,
            data: {
                ratings: review.kpis,
                generalComment: review.generalComment,
            },
        });
    };
    const generateRules = (kpis) => {
        return kpis.map((kpi) => [
            greaterThanOrEqual(kpi.minRating, $t('performance.rating_should_be_greater_than_or_equal_to_minValue', {
                minValue: kpi.minRating,
            })),
            lessThanOrEqual(kpi.maxRating, $t('performance.rating_should_be_less_than_or_equal_to_maxValue', {
                maxValue: kpi.maxRating,
            })),
        ]);
    };
    const generateModel = (kpis) => {
        return {
            kpis: kpis.map((kpi) => ({
                kpiId: kpi.id,
                rating: null,
                comment: null,
            })),
            generalComment: null,
        };
    };
    const generateEvaluationFormData = (evaluationData, generalComment, kpis) => {
        return {
            kpis: kpis.map(({ kpiId }) => {
                const _kpi = evaluationData.find((datum) => datum.kpi.id === kpiId);
                return {
                    kpiId,
                    rating: _kpi?.rating,
                    comment: _kpi?.comment,
                };
            }),
            generalComment: generalComment,
        };
    };
    const generateReviewerData = (reviewerData) => {
        return {
            details: {
                empNumber: reviewerData.employee.empNumber,
                firstName: reviewerData.employee.firstName,
                lastName: reviewerData.employee.lastName,
                middleName: reviewerData.employee.middleName,
                terminationId: reviewerData.employee.terminationId,
            },
            jobTitle: reviewerData.employee.jobTitle.name,
            status: reviewerData.status,
        };
    };
    const generateAllowedActions = (allowedActions) => {
        return new Map(allowedActions?.map((action) => {
            return [action.action, action.name];
        }));
    };
    return {
        getAllKpis,
        getEmployeeReview,
        getSupervisorReview,
        getFinalReview,
        generateRules,
        generateModel,
        generateReviewerData,
        generateAllowedActions,
        generateEvaluationFormData,
        finalizeReview,
        saveEmployeeReview,
        saveSupervisorReview,
    };
}
//# sourceMappingURL=useReviewEvaluation.js.map
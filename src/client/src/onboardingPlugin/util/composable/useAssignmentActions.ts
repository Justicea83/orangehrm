import {APIService} from '@/core/util/services/api.service';

type AssignmentAction = {
  component: string;
  props: object;
};

type AssignmentSecondaryAction = {
  label: string;
  context: string;
};

type useAssignmentActionsArgs = {
  primaryActions?: {[name: string]: AssignmentAction};
  secondaryActions?: AssignmentSecondaryAction[];
};

const approve: AssignmentAction = {
  component: 'oxd-button',
  props: {
    label: 'Approve',
    displayType: 'label-success',
    size: 'small',
    onClick: null,
  },
};

const reject: AssignmentAction = {
  component: 'oxd-button',
  props: {
    label: 'Reject',
    displayType: 'label-danger',
    size: 'small',
    onClick: null,
  },
};

const cancel: AssignmentAction = {
  component: 'oxd-button',
  props: {
    label: 'Cancel',
    displayType: 'label-warn',
    size: 'medium',
    onClick: null,
  },
};

const more: AssignmentAction = {
  component: 'oxd-table-dropdown',
  props: {
    options: [],
    style: {'margin-left': 'auto'},
    onClick: null,
  },
};

export default function useAssignmentActions(
  http: APIService,
  {
    primaryActions = {approve, reject, cancel, more},
  }: useAssignmentActionsArgs = {},
) {
  const processAssignmentAction = (id: number, actionType: string) => {
    return http.request({
      method: 'PUT',
      url: `/api/v2/Assignment/Assignments/${id}`,
      data: {
        action: actionType,
      },
    });
  };

  const processAssignmentRequestAction = (id: number, actionType: string) => {
    return http.request({
      method: 'PUT',
      url: `/api/v2/Assignment/employees/Assignment-requests/${id}`,
      data: {
        action: actionType,
      },
    });
  };

  const processAssignmentRequestBulkAction = (
    ids: number[],
    actionType: string,
  ) => {
    return http.request({
      method: 'PUT',
      url: '/api/v2/Assignment/employees/Assignment-requests/bulk',
      data: {
        data: ids.map((id) => {
          return {
            AssignmentRequestId: id,
            action: actionType,
          };
        }),
      },
    });
  };

  return {
    assignmentActions: primaryActions,
    processAssignmentAction,
    processAssignmentRequestAction,
    processAssignmentRequestBulkAction,
  };
}

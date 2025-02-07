const approve = {
  component: 'oxd-button',
  props: {
    label: 'Approve',
    displayType: 'label-success',
    size: 'small',
    onClick: null,
  },
};
const reject = {
  component: 'oxd-button',
  props: {
    label: 'Reject',
    displayType: 'label-danger',
    size: 'small',
    onClick: null,
  },
};
const cancel = {
  component: 'oxd-button',
  props: {
    label: 'Cancel',
    displayType: 'label-warn',
    size: 'medium',
    onClick: null,
  },
};
const more = {
  component: 'oxd-table-dropdown',
  props: {
    options: [],
    style: {'margin-left': 'auto'},
    onClick: null,
  },
};
export default function useAssignmentActions(
  http,
  {primaryActions = {approve, reject, cancel, more}} = {},
) {
  const performAssignmentAction = (payload) => {
    return http.request({
      method: 'PUT',
      url: '/api/v2/task-management/task-groups/actions',
      data: payload,
    });
  };
  return {
    assignmentActions: primaryActions,
    performAssignmentAction,
  };
}
//# sourceMappingURL=useAssignmentActions.js.map

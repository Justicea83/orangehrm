export const MODEL_TYPE_GROUP_ASSIGNMENT = 'groupAssignment';
export default function useComments(http) {
    const addComment = (payload) => {
        return http.request({
            method: 'POST',
            url: `/api/v2/comments`,
            data: payload,
        });
    };
    const editComment = (id, payload) => {
        return http.request({
            method: 'PUT',
            url: `/api/v2/comments/${id}`,
            data: payload,
        });
    };
    const deleteComment = (ids) => {
        return http.request({
            method: 'DELETE',
            url: `/api/v2/comments`,
            data: {
                ids,
            },
        });
    };
    return {
        addComment,
        deleteComment,
        editComment,
    };
}
//# sourceMappingURL=useComments.js.map
import {APIService} from '@/core/util/services/api.service';

export const MODEL_TYPE_GROUP_ASSIGNMENT = 'groupAssignment';

export type CommentPayload = {
  body: string;
  model_type: string;
  model_id: number;
  parent_id: number | null;
};

export default function useComments(http: APIService) {
  const addComment = (payload: CommentPayload) => {
    return http.request({
      method: 'POST',
      url: `/api/v2/comments`,
      data: payload,
    });
  };

  const editComment = (id: number, payload: CommentPayload) => {
    return http.request({
      method: 'PUT',
      url: `/api/v2/comments/${id}`,
      data: payload,
    });
  };

  const deleteComment = (ids: number[]) => {
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

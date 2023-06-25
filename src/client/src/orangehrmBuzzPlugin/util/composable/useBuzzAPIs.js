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
export default function useBuzzAPIs(http) {
    const fetchPostComments = (postId, limit = 0, detailed = false) => {
        return http.request({
            method: 'GET',
            url: `/api/v2/buzz/shares/${postId}/comments`,
            params: {
                limit: limit,
                ...(detailed && { model: 'detailed' }),
            },
        });
    };
    const savePostComment = (postId, comment) => {
        return http.request({
            method: 'POST',
            url: `/api/v2/buzz/shares/${postId}/comments`,
            data: { text: comment },
        });
    };
    const updatePostComment = (postId, commentId, comment) => {
        return http.request({
            method: 'PUT',
            url: `/api/v2/buzz/shares/${postId}/comments/${commentId}`,
            data: { text: comment },
        });
    };
    const deletePostComment = (postId, commentId) => {
        return http.request({
            method: 'DELETE',
            url: `/api/v2/buzz/shares/${postId}/comments/${commentId}`,
        });
    };
    const fetchPostLikes = (postId) => {
        return http.request({
            method: 'GET',
            url: `/api/v2/buzz/shares/${postId}/likes`,
        });
    };
    const fetchPosts = (limit, offset, sortOrder, sortField) => {
        return http.request({
            method: 'GET',
            url: '/api/v2/buzz/feed',
            params: {
                limit,
                offset,
                sortOrder,
                sortField,
            },
        });
    };
    const updatePostLike = (postId, like) => {
        return http.request({
            method: like ? 'DELETE' : 'POST',
            url: `/api/v2/buzz/shares/${postId}/likes`,
        });
    };
    const updateCommentLike = (commentId, like) => {
        return http.request({
            method: like ? 'DELETE' : 'POST',
            url: `/api/v2/buzz/comments/${commentId}/likes`,
        });
    };
    const deletePost = (postId) => {
        return http.request({
            method: 'DELETE',
            url: `/api/v2/buzz/shares/${postId}`,
        });
    };
    const updatePost = (postId, post) => {
        if (post.type === 'photo') {
            delete post['link'];
        }
        if (post.type === 'video') {
            delete post['photos'];
            delete post['deletedPhotos'];
        }
        if (post.type === 'text') {
            delete post['link'];
            delete post['photos'];
        }
        return http.request({
            method: 'PUT',
            url: `/api/v2/buzz/posts/${postId}`,
            data: { ...post },
            params: { model: 'detailed' },
        });
    };
    const updateSharedPost = (postId, text) => {
        return http.request({
            method: 'PUT',
            url: `/api/v2/buzz/shares/${postId}`,
            data: { text },
            params: { model: 'detailed' },
        });
    };
    return {
        fetchPosts,
        updatePost,
        deletePost,
        updatePostLike,
        fetchPostLikes,
        savePostComment,
        updateSharedPost,
        updatePostComment,
        deletePostComment,
        fetchPostComments,
        updateCommentLike,
    };
}
//# sourceMappingURL=useBuzzAPIs.js.map
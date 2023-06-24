<template>
  <div class="oxd-table-filter">
    <div class="oxd-table-filter-header">
      <div class="oxd-table-filter-header-title">
        <oxd-text
          class="oxd-table-filter-title cursor-pointer"
          tag="h5"
          @click="openMenu"
        >
          {{ taskGroup?.name }}
        </oxd-text>
      </div>
      <div class="oxd-table-filter-header-options">
        <div class="--toggle">
          <slot name="toggleOptions"></slot>
        </div>
        <div class="--export mr-2">
          <slot name="exportOptions"></slot>
        </div>
        <div class="--toggle">
          <oxd-icon-button
            :name="taskGroup.isActive ? 'caret-up-fill' : 'caret-down-fill'"
            @click="toggleFilters"
          />
        </div>
      </div>
    </div>
    <div v-show="taskGroup.isActive" class="oxd-table-filter-area mt-2">
      <task-group
        :is-owner="isOwner"
        :task-list="taskGroup.taskGroups"
        :task-group-id="taskGroup.id"
        :completed="taskGroup.completed"
        :submitted-at="taskGroup.submittedAt"
      />
      <comment
        v-for="comment in comments"
        :key="comment.id"
        :comment="comment"
      />

      <div class="mt-8">
        <oxd-form @submit-valid="onSave">
          <oxd-form-row>
            <oxd-input-field
              v-model="newComment.body"
              type="textarea"
              placeholder="Add Comment"
            />
          </oxd-form-row>

          <oxd-divider />

          <oxd-form-actions>
            <submit-button
              :loading="loading"
              :disabled="!newComment || !newComment.body"
              label="Add Comment"
            />
          </oxd-form-actions>
        </oxd-form>
      </div>
    </div>
  </div>
</template>

<script>
import TaskGroup from '@/onboardingPlugin/pages/my-assignments/components/TaskGroup';
import Comment from '@/core/components/comments/comment/Comment';
import SubmitButton from '@ohrm/components/buttons/SubmitButton.vue';
import {
  OxdForm,
  OxdFormRow,
  OxdIconButton,
  OxdFormActions,
  OxdDivider,
  OxdText,
  OxdInputField,
} from '@ohrm/oxd';
import {APIService} from '@/core/util/services/api.service';
import useComments, {
  MODEL_TYPE_GROUP_ASSIGNMENT,
} from '@/commentsPlugin/util/composable/useComments';

export default {
  name: 'Assignment',
  components: {
    OxdIconButton,
    OxdText,
    TaskGroup,
    Comment,
    OxdForm,
    OxdDivider,
    OxdFormActions,
    OxdFormRow,
    SubmitButton,
    OxdInputField,
  },
  props: {
    taskGroup: {
      type: Object,
      required: true,
    },
    isOwner: {
      type: Boolean,
      default: true,
    },
  },
  emits: ['open-details', 'toggleActive'],
  setup() {
    const http = new APIService(window.appGlobal.baseUrl, '');
    const {addComment, deleteComment, editComment} = useComments(http);

    return {
      addComment,
      deleteComment,
      editComment,
    };
  },
  data() {
    return {
      newComment: {},
      loading: false,
      comments: [...this.taskGroup.comments],
    };
  },

  methods: {
    openMenu() {
      this.$emit('open-details', this.taskGroup);
      this.toggleFilters();
    },
    toggleFilters() {
      this.$emit('toggleActive', this.taskGroup);
    },

    onSave() {
      const payload = {
        ...this.newComment,
        model_type: MODEL_TYPE_GROUP_ASSIGNMENT,
        model_id: this.taskGroup.id,
      };

      this.loading = true;
      this.addComment(payload)
        .then(({data}) => {
          const comment = data.data;
          this.comments.unshift(comment);
          this.newComment = {
            body: '',
          };
        })
        .finally(() => (this.loading = false));
    },
  },
};
</script>

<style src="./assignment.scss" lang="scss" scoped></style>

<style lang="scss">
.oxd-table-filter-header-options {
  & .oxd-icon-button {
    font-size: 12px;
    width: 24px !important;
    height: 24px !important;
    min-width: unset;
    min-height: unset;
    margin-right: 5px;
  }
  & .oxd-button {
    padding-right: 5px;
    padding-left: 5px;
    min-width: unset;
    margin-right: 5px;
  }
}
</style>

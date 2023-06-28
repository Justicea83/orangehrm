<template>
  <oxd-dialog class="orangehrm-dialog-modal" @update:show="onCancel">
    <div class="orangehrm-modal-header">
      <oxd-text type="card-title"> Add Comments </oxd-text>
    </div>
    <oxd-divider />
    <!--    <div v-if="!isLoading" class="orangehrm-modal-content">
      <leave-comment
        v-for="comm in comments"
        :key="comm.id"
        :data="comm"
      ></leave-comment>
    </div>-->
    <div class="orangehrm-modal-content">
      <comment v-for="c in comments" :key="c.id" :comment="c" />
    </div>

    <oxd-form :loading="isLoading" @submit-valid="onSave">
      <oxd-form-row>
        <oxd-input-field
          v-model="comment"
          type="textarea"
          :placeholder="$t('general.comment_here')"
          :rules="rules.comment"
        />
      </oxd-form-row>
      <oxd-divider />
      <oxd-form-actions>
        <oxd-button
          type="button"
          display-type="ghost"
          :label="$t('general.cancel')"
          @click="onCancel"
        />
        <submit-button />
      </oxd-form-actions>
    </oxd-form>
  </oxd-dialog>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import {required} from '@ohrm/core/util/validation/rules';
import {
  OxdForm,
  OxdFormRow,
  OxdFormActions,
  OxdDialog,
  OxdDivider,
  OxdButton,
  OxdText,
  OxdInputField,
} from '@ohrm/oxd';
import useComments from '@/commentsPlugin/util/composable/useComments';
import SubmitButton from '@/core/components/buttons/SubmitButton';
import Comment from '@/core/components/comments/comment/Comment';

export default {
  name: 'AddCommentModal',
  components: {
    'oxd-dialog': OxdDialog,
    Comment,
    OxdText,
    OxdForm,
    OxdDivider,
    OxdFormActions,
    OxdFormRow,
    SubmitButton,
    OxdInputField,
    OxdButton,
  },
  props: {
    id: {
      type: Number,
      required: false,
      default: null,
    },
    leaveRequest: {
      type: Boolean,
      default: true,
    },
    modelType: {
      type: String,
      required: true,
    },
    modelId: {
      type: Number,
      required: true,
    },
    comments: {
      type: Array,
      default: () => Array.from([]),
    },
  },
  emits: ['close'],
  setup() {
    const http = new APIService(window.appGlobal.baseUrl);
    const {addComment, deleteComment, editComment} = useComments(http);

    return {
      http,
      addComment,
      deleteComment,
      editComment,
    };
  },
  data() {
    return {
      isLoading: false,
      comment: null,
      rules: {
        comment: [required],
      },
    };
  },
  methods: {
    onSave() {
      const payload = {
        body: this.comment,
        model_type: this.modelType,
        model_id: this.modelId,
      };

      this.isLoading = true;
      this.addComment(payload)
        .then(() => {
          this.comment = null;
          this.$emit('close', true);
        })
        .finally(() => (this.isLoading = false));
    },
    onCancel() {
      this.comment = null;
      this.$emit('close', true);
    },
  },
};
</script>

<style scoped lang="scss">
.orangehrm-modal-content {
  max-height: 200px;
  overflow: hidden auto;
  margin: 0.5rem 0;
  @include oxd-scrollbar();
}
</style>

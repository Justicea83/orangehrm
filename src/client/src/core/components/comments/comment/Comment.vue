<template>
  <div>
    <div class="comment">
      <div class="comment__header mb-[10px]">
        <div class="comment__author">
          <div class="comment__avatar">
            <img :src="comment.user?.avatar ?? avatar" alt="" />
          </div>
          <div class="comment__content">
            <h3 class="comment__title">
              {{ comment.user.name }}
            </h3>
          </div>
        </div>
      </div>
      <div>
        <p class="comment__body">
          {{ comment.body }}
        </p>
      </div>
    </div>
    <div
      v-if="comment.replies && comment.replies.length"
      class="pl-[20px] comment__inner-commment"
    >
      <template v-for="(reply, index) in comment.replies" :key="index">
        <Comment
          v-bind="{
            comment: reply,
            //lastOne: index === replies.length - 1,
            //hasCorner: replies.length >= 1,
          }"
        />
      </template>
    </div>
  </div>
</template>

<script lang="ts">
import {PropType} from 'vue';
import {Comment} from '@/core/components/comments/models';

export default {
  props: {
    avatar: {
      type: String,
      default: 'https://picsum.photos/200',
    },
    comment: {
      type: Object as PropType<Comment>,
      default: () => Array.from([]),
    },
  },
};
</script>

<style lang="scss">
.comment {
  padding-top: 20px;
  // .comment__author
  position: relative;
  &.shaddow {
    box-shadow: calc(20px * -1 - 1px) 0 0 0 #fff;
  }
  &.corner::before {
    left: -21px;
    content: '';
    top: 0;
    height: 30px;
    width: 15px;
    position: absolute;
    border-left: 1px solid #e6e6e6;
    border-bottom: 1px solid #e6e6e6;
    border-bottom-left-radius: 8px;
  }
  &__author {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  // .comment__avatar
  &__avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    overflow: hidden;
    min-width: 36px;
    align-self: flex-start;
  }
  // .comment__title
  &__title {
    font-size: 15px;
    font-weight: 500;
    line-height: 16px;
  }

  // .comment__body
  &__body {
    font-size: 14px;
    font-weight: 400;
    line-height: 21px;
  }
  // .comment__wrapper
  &__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  &__inner-commment {
    border-left: 1px solid #e6e6e6;
  }
}
</style>

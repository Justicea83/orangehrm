<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <div class="wrapper">
        <div class="orangehrm-module-under-development">
          <oxd-text tag="h6" class="orangehrm-module-under-development-heading">
            A competitive HRM solution that centralizes employee data, automates
            administrative tasks, and offers various features to optimize
            workforce management for businesses of all sizes.
          </oxd-text>
          <img
            :src="moduleUnderDevelopment"
            class="orangehrm-module-under-development-img"
            alt="this module is under development"
          />
          <oxd-text
            tag="h4"
            class="orangehrm-module-under-development-soon-heading"
          >
            COMING SOON...
          </oxd-text>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      moduleUnderDevelopment: `${window.appGlobal.baseUrl}/../images/coming-soon.gif`,
    };
  },
};
</script>

<style lang="scss" scoped>
.orangehrm-card-container {
  background: $oxd-primary-one-color;
}
.wrapper {
  display: flex;
  justify-content: center;
  align-content: center;
  width: 100%;
}
.orangehrm-module-under-development {
  text-align: center;
  max-width: 80%;

  &-img {
    max-width: 100%;
    height: 100%;
    max-height: 350px;
    display: block;
    margin: 3rem auto;
    object-fit: fill;
  }

  &-heading {
    color: $oxd-secondary-font-color;
    font-weight: 300;
  }
  &-soon-heading {
    color: $oxd-secondary-font-color;
    font-weight: 700;
  }

  &-text {
    font-size: 14px;
    max-width: 800px;
    margin: 0 auto;
    margin-bottom: 2rem;
  }

  &-link {
    text-decoration: none;
    color: $oxd-primary-one-color;
  }
}
</style>

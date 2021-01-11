<template>
  <div class="card mt-4">
    <h2 class="card-header">Statistics</h2>
    <div class="card-body">
      <p>
        Your rankings improve every time you answer a question correctly. Keep
        learning and earning course points to become one of our top learners!
      </p>
      <div class="row">
        <div class="col-md-6">
          <h4 v-if="localRank">
            You are ranked <b>{{ localRank }}th</b> in {{ country }}
          </h4>
          <ul v-if="localTopTier">
            <li
              v-for="score in localTopTier"
              :key="score.id"
              class="courseRanking__rankItem"
              style="display: flex; flex-direction: row; padding: 10px"
            >
              <div
                class="position"
                style="
                  font-size: 28px;
                  color: rgb(132, 132, 132);
                  text-align: right;
                  width: 80px;
                  padding-right: 10px;
                "
              >
                {{ score.rank }}
              </div>
              <div class="info">
                <div
                  style="font-size: 16px"
                  :class="{ isBold: user.id === score.user_id }"
                >
                  {{ score.name }}
                </div>
                <div
                  class="score"
                  style="font-size: 10px; color: rgb(132, 132, 132)"
                >
                  {{ score.score }} PTS
                  <span v-if="score.last_added_score"
                    >(+{{ score.last_added_score }})</span
                  >
                </div>
              </div>
            </li>
          </ul>
          <hr v-if="localMiddleTier" />
          <ul v-if="localMiddleTier">
            <li
              v-for="score in localMiddleTier"
              :key="score.id"
              class="courseRanking__rankItem"
              style="display: flex; flex-direction: row; padding: 10px"
            >
              <div
                class="position"
                style="
                  font-size: 28px;
                  color: rgb(132, 132, 132);
                  text-align: right;
                  width: 80px;
                  padding-right: 10px;
                "
              >
                {{ score.rank }}
              </div>
              <div class="info">
                <div
                  style="font-size: 16px"
                  :class="{ isBold: user.id === score.user_id }"
                >
                  {{ score.name }}
                </div>
                <div
                  class="score"
                  style="font-size: 10px; color: rgb(132, 132, 132)"
                >
                  {{ score.score }} PTS
                  <span v-if="score.last_added_score">
                    (+{{ score.last_added_score }})</span
                  >
                </div>
              </div>
            </li>
          </ul>
          <hr v-if="localBottomTier" />
          <ul v-if="localBottomTier">
            <li
              v-for="score in localBottomTier"
              :key="score.id"
              class="courseRanking__rankItem"
              style="display: flex; flex-direction: row; padding: 10px"
            >
              <div
                class="position"
                style="
                  font-size: 28px;
                  color: rgb(132, 132, 132);
                  text-align: right;
                  width: 80px;
                  padding-right: 10px;
                "
              >
                {{ score.rank }}
              </div>
              <div class="info">
                <div
                  style="font-size: 16px"
                  :class="{ isBold: user.id === score.user_id }"
                >
                  {{ score.name }}
                </div>
                <div
                  class="score"
                  style="font-size: 10px; color: rgb(132, 132, 132)"
                >
                  {{ score.score }} PTS
                  <span v-if="score.last_added_score"
                    >(+{{ score.last_added_score }})</span
                  >
                </div>
              </div>
            </li>
          </ul>
        </div>
        <div class="col-md-6">
          <h4 v-if="globalRank">
            You are ranked <b>{{ globalRank }}th</b> Worldwide
          </h4>
          <ul v-if="globalTopTier">
            <li
              v-for="score in globalTopTier"
              :key="score.id"
              class="courseRanking__rankItem"
              style="display: flex; flex-direction: row; padding: 10px"
            >
              <div
                class="position"
                style="
                  font-size: 28px;
                  color: rgb(132, 132, 132);
                  text-align: right;
                  width: 80px;
                  padding-right: 10px;
                "
              >
                {{ score.rank }}
              </div>
              <div class="info">
                <div
                  style="font-size: 16px"
                  :class="{ isBold: user.id === score.user_id }"
                >
                  {{ score.name }}
                </div>
                <div
                  class="score"
                  style="font-size: 10px; color: rgb(132, 132, 132)"
                >
                  {{ score.score }} PTS
                  <span v-if="score.last_added_score"
                    >(+{{ score.last_added_score }})</span
                  >
                </div>
              </div>
            </li>
          </ul>
          <hr v-if="globalMiddleTier" />
          <ul v-if="globalMiddleTier">
            <li
              v-for="score in globalMiddleTier"
              :key="score.id"
              class="courseRanking__rankItem"
              style="display: flex; flex-direction: row; padding: 10px"
            >
              <div
                class="position"
                style="
                  font-size: 28px;
                  color: rgb(132, 132, 132);
                  text-align: right;
                  width: 80px;
                  padding-right: 10px;
                "
              >
                {{ score.rank }}
              </div>
              <div class="info">
                <div
                  style="font-size: 16px"
                  :class="{ isBold: user.id === score.user_id }"
                >
                  {{ score.name }}
                </div>
                <div
                  class="score"
                  style="font-size: 10px; color: rgb(132, 132, 132)"
                >
                  {{ score.score }} PTS
                  <span v-if="score.last_added_score">
                    (+{{ score.last_added_score }})</span
                  >
                </div>
              </div>
            </li>
          </ul>
          <hr v-if="globalBottomTier" />
          <ul v-if="globalBottomTier">
            <li
              v-for="score in globalBottomTier"
              :key="score.id"
              class="courseRanking__rankItem"
              style="display: flex; flex-direction: row; padding: 10px"
            >
              <div
                class="position"
                style="
                  font-size: 28px;
                  color: rgb(132, 132, 132);
                  text-align: right;
                  width: 80px;
                  padding-right: 10px;
                "
              >
                {{ score.rank }}
              </div>
              <div class="info">
                <div
                  style="font-size: 16px"
                  :class="{ isBold: user.id === score.user_id }"
                >
                  {{ score.name }}
                </div>
                <div
                  class="score"
                  style="font-size: 10px; color: rgb(132, 132, 132)"
                >
                  {{ score.score }} PTS
                  <span v-if="score.last_added_score"
                    >(+{{ score.last_added_score }})</span
                  >
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    globalTopTier() {
      const globalTopTier =
        this.scoreStatistics.globalTiers.topTier &&
        this.scoreStatistics.globalTiers.topTier.length > 0
          ? this.scoreStatistics.globalTiers.topTier
          : null;
      return globalTopTier;
    },
    globalMiddleTier() {
      const globalMiddleTier =
        this.scoreStatistics.globalTiers.middleTier &&
        this.scoreStatistics.globalTiers.middleTier.length > 0
          ? this.scoreStatistics.globalTiers.middleTier
          : null;
      return globalMiddleTier;
    },
    globalBottomTier() {
      const globalBottomTier =
        this.scoreStatistics.globalTiers.bottomTier &&
        this.scoreStatistics.globalTiers.bottomTier.length > 0
          ? this.scoreStatistics.globalTiers.bottomTier
          : null;
      return globalBottomTier;
    },
    localTopTier() {
      const localTopTier =
        this.scoreStatistics.localTiers.topTier &&
        this.scoreStatistics.localTiers.topTier.length > 0
          ? this.scoreStatistics.localTiers.topTier
          : null;
      return localTopTier;
    },
    localMiddleTier() {
     const localMiddleTier =
        this.scoreStatistics.localTiers.middleTier &&
        this.scoreStatistics.localTiers.middleTier.length > 0
          ? this.scoreStatistics.localTiers.middleTier
          : null;
      return localMiddleTier;
    },
    localBottomTier() {
      const localBottomTier =
        this.scoreStatistics.localTiers.bottomTier &&
        this.scoreStatistics.localTiers.bottomTier.length > 0
          ? this.scoreStatistics.localTiers.bottomTier
          : null;
      return localBottomTier;
    },
    globalRank() {
      const globalRank = this.scoreStatistics.globalTiers.userRank;
      return globalRank ? globalRank : "";
    },
    localRank() {
      const localRank = this.scoreStatistics.localTiers.userRank;
      return localRank ? localRank : "";
    },
    country() {
      return this.scoreStatistics.user.country.name;
    },
    user() {
      return this.scoreStatistics.user;
    },
  },
  props: {
    scoreStatistics: {
      required: true,
    },
  },
  mounted() {
    // Echo.channel('statistics')
    //     .listen('quiz-evaluated', (e) => {
    //         alert(e);
    //     });
  },
};
</script>
<style scoped>
.isBold {
  font-weight: bold;
}
</style>

<template>
  <div>
      <ul>
          <li v-for="channel in channels">
              <router-link :to="'/channels/' + channel.uid"
                           v-text="channel.name"/>
          </li>
      </ul>

      <router-view :key="$route.fullPath"/>
  </div>
</template>

<script>
    export default {
        data() {
            return {
                channels: []
            }
        },

        mounted() {
            this.$http.get('/api/microsub?action=channels')
                .then(response => this.channels = response.data.channels)
        }
    }
</script>

<style lang="sass" scoped>
    div
        max-width: 800px
        margin: 0 auto

    li
        display: inline-block
        margin-right: 1em
</style>

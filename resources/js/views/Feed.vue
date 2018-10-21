<template>
  <div>
      <h1>Feed</h1>
      <div v-for="entry in feed" class="box">
          <!--:class="{'has-background-light': entry._is_read}">-->
          <article class="media">
              <div class="media-left">
                  <figure class="image is-64x64" v-if="entry.author">
                      <img :src="entry.author.photo" :alt="entry.author.photo">
                  </figure>
              </div>
              <div class="media-content">
                  <div v-if="entry.author">
                      <strong v-text="entry.author.name"></strong>
                      <small v-text="entry.author.url"></small>
                      <small v-text="entry.published"></small>
                  </div>

                  <div class="content">
                      <h1 v-text="entry.name"></h1>

                      <div v-if="entry.content"
                           v-html="entry.content.html ? entry.content.html : entry.content.text "></div>
                  </div>
              </div>
          </article>
      </div>
  </div>
</template>

<script>
  export default {

      data() {
          return {
              feed: []
          }
      },

      mounted() {
          return this.$http.get('/api/microsub?action=timeline&channel=' + this.$route.params.id)
              .then(response => this.feed = response.data.items)
      }
  }
</script>

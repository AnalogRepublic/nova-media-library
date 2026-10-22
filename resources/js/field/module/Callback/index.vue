<template>
  <div>
    <a class="cursor-pointer dim inline-block text-primary font-bold mt-2" @click="popup = true">
      {{ __('Media Library') }}
    </a>

    <Library v-if="popup" :field="field" />
  </div>
</template>

<script>
import Library from '../Library'

export default {
  props: ['field'],

  components: { Library },

  data() {
    return { popup: false }
  },

  methods: {
    select(array) {
      let cb = this.field.nmlJsCallback
      if ('object' === typeof cb && cb[0] && window[cb[0]]) eval(cb[0])(array, cb[1])
    },
  },

  created() {
    Nova.$on(`nmlSelectFiles[${this.field.attribute}]`, array => {
      this.popup = false
      this.select(array)
    })
  },

  beforeUnmount() {
    Nova.$off(`nmlSelectFiles[${this.field.attribute}]`)
  },
}
</script>

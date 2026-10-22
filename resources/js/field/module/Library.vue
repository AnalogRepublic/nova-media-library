<template>
  <div class="popup fixed inset-0 z-40 py-6 bg-primary-70% overflow-y-auto">
    <div class="absolute inset-0 z-20" @click="$parent.popup = null"></div>

    <div class="relative z-30 bg-white p-8 rounded-lg shadow-lg m-auto">
      <button type="button" class="nml-close select-none" @click="$parent.popup = null">&times;</button>

      <Library :field="field.attribute" :isArray="field.nmlArray" :types="types" />
    </div>
  </div>
</template>

<script>
import Library from '../../tool'

export default {
  props: ['field'],

  components: { Library },

  computed: {
    types() {
      return this.field && Array.isArray(this.field.nmlTypes) ? this.field.nmlTypes : []
    },
  },

  created() {
    console.log('[nml:library] this.field =', this.field)
    console.log('[nml:library] this.$props.field =', this.$props.field)
    console.log('[nml:library] this.$attrs.field =', this.$attrs.field)
  },

  mounted() {
    document.body.classList.add('overflow-hidden')
  },

  beforeUnmount() {
    document.body.classList.remove('overflow-hidden')
  },
}
</script>

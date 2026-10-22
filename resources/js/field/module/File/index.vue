<template>
  <div>
    <div
      class="card border border-lg border-50 max-w-xs p-8 text-center cursor-pointer"
      v-if="!item && isForm"
      @click="popup = true"
    >
      {{ __('Select File') }}
    </div>

    <a v-else-if="item" :href="item.url" target="_blank" class="no-underline">
      <img
        class="block rounded-lg shadow-md max-w-xs"
        v-if="`image` === mime(item)"
        :src="item.preview || item.url"
        :alt="__('This file could not be found')"
      />

      <div class="nml-display-list" v-else>
        <div class="nml-item relative mb-2 cursor-pointer" :title="item.title || item.name">
          <div :class="'icon rounded-lg shadow-md nml-icon-' + mime(item)" :style="bg(item)" />
          <div class="title truncate" v-text="item.title || item.name" />
        </div>
      </div>
    </a>

    <div class="mt-4" v-if="isForm && item">
      <a class="cursor-pointer dim inline-block text-primary font-bold" @click="popup = true">
        {{ __('Media Library') }}
      </a>

      <a class="cursor-pointer dim inline-block text-danger font-bold ml-8" @click="changeFile(null)">
        {{ __('Clear') }}
      </a>
    </div>

    <transition name="fade" mode="out-in">
      <Library v-if="popup" :field="field" />
    </transition>
  </div>
</template>

<script>
import Library from '../Library'
import Mixin from '../../../_mixin'

export default {
  props: ['field', 'handler'],

  mixins: [Mixin],

  components: { Library },

  data() {
    return {
      popup: false,
      item: this.field.value,
    }
  },

  computed: {
    isForm() {
      return typeof this.handler === 'function'
    },
  },

  methods: {
    changeFile(item) {
      this.item = item
      if (this.handler) this.handler(item)
    },
  },

  created() {
    Nova.$on(`nmlSelectFiles[${this.field.attribute}]`, array => {
      this.popup = false
      this.changeFile(array[0])
    })
  },

  beforeUnmount() {
    Nova.$off(`nmlSelectFiles[${this.field.attribute}]`)
  },
}
</script>

<template>
  <div>
    <draggable
      :class="'flex flex-wrap nml-display-' + type"
      v-if="array && array.length"
      v-model="array"
      item-key="id"
      @end="changeArray(array)"
      :disabled="!isForm"
    >
      <template #item="{ element, index }">
        <div class="nml-item relative mb-2 cursor-pointer">
          <div :class="'icon rounded-lg shadow-md nml-icon-' + mime(element)" :style="bg(element)" />

          <div class="title truncate" v-text="element.title || element.name" />

          <a :href="element.url" target="_blank" class="absolute inset-0"></a>

          <span v-if="isForm" class="delete shadow-md dim" @click="remove(index)">
            <Icon type="trash" :solid="true" />
          </span>
        </div>
      </template>
    </draggable>

    <div
      class="card border border-lg border-50 max-w-xs p-8 text-center cursor-pointer"
      v-else-if="isForm"
      @click="popup = true"
    >
      {{ __('Select Files') }}
    </div>

    <div class="mt-4" v-if="isForm && array && array.length">
      <a class="cursor-pointer dim inline-block text-primary font-bold" @click="popup = true">
        {{ __('Media Library') }}
      </a>

      <a class="cursor-pointer dim inline-block text-danger font-bold ml-8" @click="changeArray([])">
        {{ __('Clear') }}
      </a>
    </div>

    <transition name="fade" mode="out-in">
      <Library v-if="popup" isArray :field="field" />
    </transition>
  </div>
</template>

<script>
import draggable from 'vuedraggable'
import Library from '../Library'
import Mixin from '../../../_mixin'

export default {
  props: ['field', 'handler'],

  mixins: [Mixin],

  components: { draggable, Library },

  data() {
    let type = this.field.nmlArray
    if ('auto' === type) {
      type = 'list' === localStorage.getItem('nml-display') ? 'list' : 'gallery'
    }

    return {
      popup: false,
      array: [],
      type,
    }
  },

  computed: {
    isForm() {
      return typeof this.handler === 'function'
    },
  },

  methods: {
    changeArray(array) {
      this.array = array || []
      if (this.handler) this.handler(array)
    },

    remove(num) {
      this.changeArray(this.array.slice().filter((item, i) => i !== num))
    },
  },

  created() {
    Nova.$on(`nmlSelectFiles[${this.field.attribute}]`, array => {
      this.popup = false
      this.array = this.array.concat(array)
      this.changeArray(this.array)
    })

    try {
      if (Array.isArray(this.field.value)) this.array = this.field.value
    } catch (e) {}
  },

  beforeUnmount() {
    Nova.$off(`nmlSelectFiles[${this.field.attribute}]`)
  },
}
</script>

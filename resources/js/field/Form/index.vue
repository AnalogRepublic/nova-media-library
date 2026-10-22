<template>
  <DefaultField
    :field="field"
    :errors="errors"
    :full-width-content="true"
  >
    <template #field>
      <div class="nml-field-form">
        <span
          class="cursor-pointer dim inline-block text-primary font-bold pt-2 leading-tight"
          v-if="isHidden && field.value"
          @click="isHidden = false"
        >{{ __('Show') }}</span>

        <template v-else>
          <nmlCallback v-if="field.nmlJsCallback" :field="field" />
          <nmlTrix v-else-if="field.nmlTrix" :field="field" />
          <nmlArray v-else-if="field.nmlArray" :field="field" :handler="handleChange" />
          <nmlFile v-else :field="field" :handler="handleChange" />
        </template>
      </div>
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

import nmlArray from '../module/Array'
import nmlCallback from '../module/Callback'
import nmlFile from '../module/File'
import nmlTrix from '../module/Trix'

export default {
  mixins: [FormField, HandlesValidationErrors],

  components: { nmlArray, nmlFile, nmlCallback, nmlTrix },

  props: ['resourceName', 'resourceId', 'field'],

  data() {
    return {
      isHidden: this.field.nmlHidden === true,
    }
  },

  methods: {
    setInitialValue() {
      this.value = this.field.value || null
    },

    fill(formData) {
      let data = null

      if (this.value) {
        if (this.field.nmlArray && Array.isArray(this.value)) {
          data = this.value.map(item => item.id)
        } else if (
          !this.field.nmlArray &&
          'object' === typeof this.value &&
          this.value.id
        ) {
          data = this.value.id
        }

        if (Array.isArray(data)) data = JSON.stringify(data)
      }

      formData.append(this.field.attribute, data)
    },

    handleChange(value) {
      this.value = value
    },
  },
}
</script>

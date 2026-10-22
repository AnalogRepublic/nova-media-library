<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <div class="nml-field-form">
        <template v-if="!field.value">&mdash;</template>

        <span
          class="cursor-pointer dim text-primary font-bold"
          v-else-if="isHidden"
          @click="isHidden = false"
        >{{ __('Show') }}</span>

        <template v-else-if="!field.nmlJsCallback && !field.nmlTrix">
          <nmlArray v-if="field.nmlArray" :field="field" />
          <nmlFile v-else :field="field" />
        </template>
      </div>
    </template>
  </PanelItem>
</template>

<script>
import nmlArray from '../module/Array'
import nmlCallback from '../module/Callback'
import nmlFile from '../module/File'
import nmlTrix from '../module/Trix'

export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

  components: { nmlArray, nmlFile, nmlCallback, nmlTrix },

  data() {
    return {
      isHidden: this.field.nmlHidden === true,
    }
  },
}
</script>

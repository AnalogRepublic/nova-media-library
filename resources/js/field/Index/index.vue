<template>
  <div class="nml-field-index nml-item">
    <template v-if="!item">&mdash;</template>
    <template v-else>
      <a
        :href="item.url"
        target="_blank"
        :class="`block icon shadow-md relative nml-icon-` + mime(item)"
        :style="bg(item)"
      >
        <div v-if="count > 1" class="count">{{ count }}</div>
      </a>
    </template>
  </div>
</template>

<script>
import Mixin from '../../_mixin'

export default {
  props: ['resourceName', 'field'],

  mixins: [Mixin],

  data() {
    return {
      count: 0,
      item: null,
    }
  },

  created() {
    let field = this.field

    if (!field.value) return

    if (Array.isArray(field.value) && field.value.length > 0) {
      this.count = field.value.length
      this.item = field.value[0]
    } else if ('object' === typeof field.value) {
      this.item = field.value
    }
  },
}
</script>

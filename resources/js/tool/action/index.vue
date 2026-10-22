<template>
  <div class="flex flex-wrap mb-4 select-none">
    <CheckboxWithLabel
      class="cursor-pointer mr-4"
      v-if="!$parent.field || $parent.isArray"
      :checked="$parent.bulk.enable"
      @input="changeBulk"
    >{{ __('Bulk Select') }}</CheckboxWithLabel>

    <button
      class="btn-default bg-primary text-white cursor-pointer shadow-md max-w-full mr-4"
      type="button"
      @click="bulkAll"
      v-if="$parent.bulk.enable"
    >{{ __('Select All') }}</button>

    <template v-if="$parent.bulkLen()">
      <button
        class="btn-default bg-danger text-white cursor-pointer shadow-md max-w-full mr-4"
        type="button"
        @click="$parent.deleteFiles(Object.keys($parent.bulk.ids))"
      >
        {{ __('Delete') }} ({{ $parent.bulkLen() }})
      </button>

      <button
        class="btn-default bg-success text-white cursor-pointer shadow-md max-w-full mr-4"
        v-if="$parent.field && $parent.bulk.enable && $parent.bulkLen()"
        type="button"
        @click="pushFiles"
      >
        {{ __('Choose') }} ({{ $parent.bulkLen() }})
      </button>
    </template>

    <label class="btn-default bg-90 text-white text-center cursor-pointer shadow-md max-w-full ml-auto">
      <input
        id="nml_upload"
        class="form-file-input"
        type="file"
        multiple
        :accept="$parent.config.accept"
        @change="selectFiles"
      />
      {{ __('Upload') }}
    </label>
  </div>
</template>

<script>
export default {
  data() {
    return {
      files: [],
      upload: {},
    }
  },

  methods: {
    clearUpload(length = 0) {
      this.upload = { total: length, done: 0 }
    },

    selectFiles(input) {
      if (!input.target.files.length) return
      this.$parent.loading = true
      this.clearUpload(input.target.files.length)

      this.files = Object.assign({}, input.target.files)
      this.uploadFile(0)

      document.getElementById('nml_upload').value = null
    },

    uploadFile(i) {
      let file = this.files[i]
      if (!file) return this.uploadCheck()

      let config = { headers: { 'Content-Type': 'multipart/form-data' } }
      let data = new FormData()
      data.append('file', file)
      data.append('folder', this.$parent.filter.folder)

      Nova.request()
        .post('/nova-vendor/nova-media-library/upload', data, config)
        .then(r => {
          this.upload.done++
          Nova.info(this.upload.done + ' / ' + this.upload.total)
          this.uploadFile(i + 1)
          if (r.data.message) Nova.success(r.data.message)
        })
        .catch(e => {
          this.uploadFile(i + 1)
          if (e.response && 422 === e.response.status && e.response.data.message) {
            Nova.error(e.response.data.message)
          }
        })
    },

    uploadCheck() {
      this.$parent.loading = false
      Nova.success(this.__('Uploaded') + ': ' + this.upload.done + '/' + this.upload.total)
      this.$parent.clearData()
      this.$parent.get()
    },

    changeBulk() {
      this.$parent.bulk.ids = {}
      this.$parent.bulk.enable = !this.$parent.bulk.enable
    },

    bulkAll() {
      if (this.$parent.bulkLen() === this.$parent.items.array.length) {
        this.$parent.bulk.ids = {}
      } else {
        this.$parent.items.array.forEach(item => {
          this.$parent.bulk.ids[item.id] = item
        })
      }
    },

    pushFiles() {
      let data = this.$parent.bulk.ids,
        array = []
      for (let key in data) array.push(data[key])

      Nova.$emit(`nmlSelectFiles[${this.$parent.field}]`, array)
    },
  },
}
</script>

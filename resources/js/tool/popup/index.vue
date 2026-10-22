<template>
  <div class="popup fixed inset-0 z-20 py-6 bg-primary-70% overflow-y-auto">
    <div class="absolute inset-0 z-20" @click="$parent.item = null"></div>

    <div class="relative z-30 bg-white p-8 rounded-lg shadow-lg m-auto">
      <button type="button" class="nml-close select-none" @click="$parent.item = null">&times;</button>

      <div class="flex border-b border-40">
        <div class="w-1/4 py-4 font-bold text-80">{{ __('ID') }}</div>
        <div class="w-3/4 py-4">{{ $parent.item.id }}</div>
      </div>

      <div class="flex border-b border-40">
        <div class="w-1/4 py-4 font-bold text-80">{{ __('Uploaded') }}</div>
        <div class="w-3/4 py-4">{{ $parent.item.created }}</div>
      </div>

      <div class="flex border-b border-40">
        <div class="w-1/4 py-4 font-bold text-80">{{ __('Type') }}</div>
        <div class="w-3/4 py-4">{{ $parent.item.type }}</div>
      </div>

      <div class="flex border-b border-40">
        <div class="w-1/4 py-4 font-bold text-80">{{ __('Size') }}</div>
        <div class="w-3/4 py-4">{{ $parent.item.options.size }}</div>
      </div>

      <div class="flex border-b border-40">
        <div class="w-1/4 py-4 font-bold text-80 leading-36">{{ __('Title') }}</div>
        <div class="w-3/4 py-4">
          <input class="form-control form-input w-full shadow-md" v-model="$parent.item.title">
        </div>
      </div>

      <div class="flex border-b border-40" v-if="$parent.config.can_private">
        <div class="w-1/4 py-4 font-bold text-80">{{ __('Private') }}</div>
        <div class="w-3/4 py-4">
          <Checkbox
            class="cursor-pointer"
            :checked="$parent.item.private"
            @input="onPrivate"
          />
        </div>
      </div>

      <div class="flex border-b border-40" v-if="'folders' === $parent.config.store">
        <div class="w-1/4 py-4 font-bold text-80 leading-36">{{ __('Move to folder') }}</div>
        <div class="w-3/4 py-4">
          <select v-model="folder" class="shadow-md block border-0 cursor-pointer form-control form-select w-full">
            <option :value="null"></option>
            <option v-for="key in folders" :value="key" :key="key">{{ key }}</option>
          </select>
        </div>
      </div>

      <div class="flex border-b border-40">
        <div class="w-1/4 py-4 font-bold text-80">{{ __('Url') }}</div>
        <div class="w-3/4 py-4">
          <a class="text-primary font-bold no-underline" :href="$parent.item.url" target="_blank">{{ __('Open') }}</a>
          <button type="button" class="text-80 font-bold no-underline float-right" @click="copyUrl">{{ __('Copy') }}</button>
        </div>
      </div>

      <div class="flex mt-6">
        <button type="button" class="btn-default btn-primary cursor-pointer mr-6 shadow-md" @click="update">
          {{ __('Update') }}
        </button>

        <button
          type="button"
          class="btn-default bg-success text-white cursor-pointer mr-6 shadow-md"
          @click="$parent.popup = 'crop'"
          v-if="$parent.config.front_crop && 'image' === $parent.item.options.mime"
        >
          {{ __('Edit Image') }}
        </button>

        <button
          type="button"
          class="btn-default btn-danger cursor-pointer ml-auto shadow-md"
          @click="$parent.deleteFiles([$parent.item.id])"
        >
          {{ __('Delete') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return { folder: null }
  },

  computed: {
    folders() {
      return this.getFolders(this.$parent.config.folders, '/', ['/'])
    },
  },

  methods: {
    getFolders(obj, path, array) {
      for (let i in obj) {
        array.push(path + i + '/')
        if ('object' === typeof obj[i]) array = this.getFolders(obj[i], path + i + '/', array)
      }
      return array
    },

    onPrivate(e) {
      this.$parent.item.private = e.target.checked
    },

    copyUrl() {
      navigator.clipboard.writeText(this.$parent.item.url).then(() => {
        Nova.success(this.__('URL has been copied'))
      })
    },

    update() {
      let cp = this.$parent.config.can_private
      this.$parent.loading = true
      let data = { id: this.$parent.item.id, title: this.$parent.item.title, folder: this.folder }
      if (cp) data.private = Boolean(this.$parent.item.private)

      Nova.request()
        .post('/nova-vendor/nova-media-library/update', data)
        .then(r => {
          Nova.success(this.__('Successfully updated'))
          this.$parent.loading = false
          this.$parent.item = null
          if (this.folder || cp) {
            this.$parent.clearData()
            this.$parent.get()
            this.folder = null
          } else {
            let index = this.$parent.items.array.findIndex(x => x.id === r.data.id)
            if (index > -1 && r.data.id) {
              r.data.url += '?' + Date.now()
              this.$parent.items.array[index] = r.data
            }
          }
        })
        .catch(e => {
          this.$parent.loading = false
          if (e.response && 422 === e.response.status && e.response.data.message) {
            Nova.error(e.response.data.message)
          }
        })
    },
  },

  mounted() {
    document.body.classList.add('overflow-hidden')
  },

  beforeUnmount() {
    document.body.classList.remove('overflow-hidden')
  },
}
</script>

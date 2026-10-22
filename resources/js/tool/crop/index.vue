<template>
  <div id="nml_cropper" class="popup fixed inset-0 z-20 py-6 bg-primary-70% overflow-y-auto">
    <div class="absolute inset-0 z-20" @click="$parent.popup = 'info'"></div>

    <div class="relative z-30 bg-white overflow-hidden rounded-lg shadow-lg m-auto">
      <div><img :src="$parent.item.url + '?' + Date.now()" id="cropper-img" /></div>

      <div class="flex border-b border-50 leading-loose">
        <div class="py-2 px-4">{{ __('Width x Height') }}</div>
        <div class="ml-auto py-2 px-4 font-bold text-90 text-right">{{ info.width || 0 }} x {{ info.height || 0 }}</div>
      </div>

      <div class="flex border-b border-50 leading-loose">
        <div class="py-2 px-4">{{ __('Rotate') }}</div>
        <div class="ml-auto py-2 px-4 font-bold text-90 text-right">
          <input class="form-control-sm form-input shadow-md" type="number" min="0" max="360" v-model="info.rotate" @input="rotate" />
        </div>
      </div>

      <div class="flex py-4 px-4">
        <template v-if="info.width && info.height">
          <button type="button" class="btn-default btn-primary cursor-pointer mr-4 shadow-md" @click="save(0)">
            {{ __('Save as new') }}
          </button>

          <button type="button" class="btn-default btn-primary cursor-pointer mr-4 shadow-md" @click="save(1)">
            {{ __('Overwrite existing') }}
          </button>
        </template>

        <button type="button" class="btn-default cursor-pointer bg-success text-white ml-auto px-3 shadow-md" @click="crop.clear().reset()">x1</button>
        <button type="button" class="btn-default btn-danger cursor-pointer ml-4 shadow-md" @click="$parent.popup = 'info'">
          {{ __('Cancel') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import Cropper from 'cropperjs'

export default {
  data() {
    return {
      img: null,
      crop: null,
      info: { rotate: 0 },
    }
  },

  methods: {
    rotate() {
      this.crop.rotateTo(parseInt(this.info.rotate || 0))
    },

    save(over) {
      this.info.over = over
      this.info.id = this.$parent.item.id
      this.$parent.loading = true
      Nova.request()
        .post('/nova-vendor/nova-media-library/crop', this.info)
        .then(() => {
          Nova.success(this.__('Image cropped successfully'))
          this.$parent.clearData()
          this.$parent.get()
          this.$parent.item = null
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
    let el = this
    el.img = document.getElementById('cropper-img')
    el.crop = new Cropper(el.img, {
      autoCrop: false,
      checkCrossOrigin: false,
      guides: false,
      viewMode: 1,
      crop(e) {
        el.info = {
          x: e.detail.x,
          y: e.detail.y,
          width: parseInt(e.detail.width),
          height: parseInt(e.detail.height),
          rotate: e.detail.rotate,
        }
      },
    })
  },

  beforeUnmount() {
    document.body.classList.remove('overflow-hidden')
    if (this.crop) this.crop.destroy()
  },
}
</script>

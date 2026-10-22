<template>
  <div class="flex flex-wrap items-center gap-4 mb-6 select-none">
    <div class="relative">
      <Icon type="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
      <input
        class="rounded-md border border-gray-300 bg-white shadow-sm pl-9 pr-3 py-2 text-sm w-full focus:border-primary-500 focus:ring-0"
        type="search"
        :placeholder="__('Search by name')"
        v-model="$parent.filter.title"
        @input="$parent.doSearch()"
      />
    </div>

    <input
      class="rounded-md border border-gray-300 bg-white shadow-sm px-3 py-2 text-sm w-44"
      type="date"
      :placeholder="__('Upload From')"
      v-model="$parent.filter.from"
      @change="$parent.doSearch()"
    />

    <input
      class="rounded-md border border-gray-300 bg-white shadow-sm px-3 py-2 text-sm w-44"
      type="date"
      :placeholder="__('Upload To')"
      v-model="$parent.filter.to"
      @change="$parent.doSearch()"
    />

    <div
      :title="__('Change Display Type')"
      class="nml-display bg-white shadow-md rounded-lg cursor-pointer ml-auto"
    >
      <svg v-if="'gallery' === $parent.config.display" @click="display('list')" style="padding:8px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M3 1h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3h-4zM3 11h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4h-4z"></path>
      </svg>
      <svg v-else @click="display('gallery')" style="padding:6px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5c0-1.1.9-2 2-2zm14 8V5H5v6h14zm0 2H5v6h14v-6zM8 9a1 1 0 1 1 0-2 1 1 0 0 1 0 2zm0 8a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
      </svg>
    </div>

    <select
      class="rounded-md border border-gray-300 bg-white shadow-sm py-2 px-3 text-sm cursor-pointer"
      v-if="types.length > 1"
      v-model="$parent.filter.type"
      @change="$parent.doSearch()"
    >
      <option :value="all">{{ __('All Types') }}</option>
      <option v-for="key in types" :value="[key]" :key="key">{{ __(key) }}</option>
    </select>
  </div>
</template>

<script>
export default {
  data() {
    let all = null
    let types = this.$parent.field ? this.$parent.types || [] : []
    if (!types.length) {
      types = this.$parent.config.types
    } else {
      all = types
    }

    return { all, types }
  },

  methods: {
    display(val) {
      this.$parent.config.display = val
      localStorage.setItem('nml-display', val)
    },
  },

  created() {
    this.$parent.filter.type = this.all
  },
}
</script>

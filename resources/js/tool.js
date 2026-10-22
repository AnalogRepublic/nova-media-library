import Tool from './tool/index.vue'
import IndexField from './field/Index'
import DetailField from './field/Detail'
import FormField from './field/Form'

Nova.booting((app, store) => {
    // Expose a global translation helper to all components.
    app.config.globalProperties.__ = (key, replace) => {
        const translations = Nova.config('translations') || {}

        return translations[key] || key
    }

    app.component('index-media-library-field', IndexField)
    app.component('detail-media-library-field', DetailField)
    app.component('form-media-library-field', FormField)

    Nova.inertia('NovaMediaLibrary', Tool)
})

if ('object' === typeof Nova.config('novaMediaLibrary')) {
    if (Nova.config('novaMediaLibrary').store === 'folders') {
        Nova.request().get('/nova-vendor/nova-media-library/folders').then(r => {
            Object.assign(Nova.config('novaMediaLibrary'), { folders: r.data })
        }).catch(() => {})
    }
    if ('object' === typeof Nova.config('novaMediaLibrary').lang) {
        Object.assign(Nova.config('translations'), Nova.config('novaMediaLibrary').lang)
    }
}

import intersect from '@alpinejs/intersect'
import collapse from '@alpinejs/collapse'
import persist from '@alpinejs/persist'
import morph from '@alpinejs/morph'
import focus from '@alpinejs/focus'
import mask from '@alpinejs/mask'
import ui from '@alpinejs/ui'
import Alpine from 'alpinejs'

Alpine.plugin(intersect)
Alpine.plugin(collapse)
Alpine.plugin(persist)
Alpine.plugin(focus)
Alpine.plugin(morph)
Alpine.plugin(mask)
Alpine.plugin(ui)

window.Alpine = Alpine
Alpine.start()

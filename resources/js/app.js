import Alpine from 'alpinejs'
import cartDrawer from './components/cartDrawer'
import '../images/other_photos/hero_section.webp';
import '../images/other_photos/flowstate_logo.png';
import.meta.glob([
    '../images/Product_photos/**/*.webp',
    '../images/Product_photos/**/*.png',
]);


window.Alpine = Alpine
window.cartDrawer = cartDrawer

Alpine.start()

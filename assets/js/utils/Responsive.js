export const getBodyHeight = () => {
    return window.getComputedStyle(document.documentElement).getPropertyValue('--bodyHeight');
}
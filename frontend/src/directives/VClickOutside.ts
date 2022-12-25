import { DirectiveBinding } from "vue"

interface FocusableElement extends HTMLElement {
    clickOutsideEventHandler(event: Event): void
}

export default {
    beforeMount: function (el: FocusableElement, binding: DirectiveBinding) {
        // Define ourClickEventHandler
        const ourClickEventHandler = (event: Event) => {
            if (!el.contains(<Node>event.target) && el !== event.target) {
                // as we are attaching an click event listern to the document (below)
                // ensure the events target is outside the element or a child of it
                binding.value(event) // before binding it
            }
        }
        // attached the handler to the element so we can remove it later easily
        el.clickOutsideEventHandler = ourClickEventHandler

        // attaching ourClickEventHandler to a listener on the document here
        document.addEventListener("click", ourClickEventHandler)
    },
    unmounted: function (el: FocusableElement) {
        // Remove Event Listener
        document.removeEventListener("click", el.clickOutsideEventHandler)
    },
    name: 'click-outside'
}

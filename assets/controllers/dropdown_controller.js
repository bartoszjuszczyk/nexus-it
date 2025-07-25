import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["menu"];

    connect() {
        this.closeOnOutsideClickHandler = this.closeOnOutsideClick.bind(this);
    }

    toggle() {
        if (this.menuTarget.classList.contains('is-active')) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        this.menuTarget.classList.add('is-active');
        document.addEventListener("click", this.closeOnOutsideClickHandler);
    }

    close() {
        this.menuTarget.classList.remove('is-active');
        document.removeEventListener("click", this.closeOnOutsideClickHandler);
    }

    closeOnOutsideClick(event) {
        if (!this.element.contains(event.target)) {
            this.close();
        }
    }

    disconnect() {
        this.close();
    }
}

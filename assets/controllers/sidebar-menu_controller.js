import {Controller} from '@hotwired/stimulus'

export default class extends Controller {
    toggle(event) {
        event.preventDefault();

        this.element.classList.toggle('is-open');
    }
}

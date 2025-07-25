import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.timeout = setTimeout(() => {
            this.close();
        }, 5000);
    }

    close() {
        clearTimeout(this.timeout);
        this.element.style.opacity = '0';
        
        setTimeout(() => {
            this.element.remove();
        }, 300);
    }
}

import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["input", "container"];

    updateList() {
        this.containerTarget.innerHTML = '';

        if (this.inputTarget.files.length > 0) {
            const list = document.createElement('ul');
            for (const file of this.inputTarget.files) {
                const listItem = document.createElement('li');
                listItem.innerHTML = `<i class="fa-solid fa-file"></i> ${file.name}`;
                list.appendChild(listItem);
            }
            this.containerTarget.appendChild(list);
        }
    }
}

import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        "switch", "enableModal", "enableModalBackdrop", "qrCodeImage", "disableModal", "disableModalBackdrop",
        "authCodeSpan", "authCodeInput"
    ];

    static values = {
        generateUrl: String
    };

    toggle(event) {
        const originalState = !event.currentTarget.checked;

        if (event.currentTarget.checked) {
            this.openEnableModal(originalState);
        } else {
            this.openDisableModal(originalState);
        }
    }

    async openEnableModal(originalState) {
        this.switchTarget.dataset.originalState = originalState;

        this.qrCodeImageTarget.src = '/assets/images/loader.gif';
        this.enableModalTarget.classList.add('is-visible');
        this.enableModalBackdropTarget.classList.add('is-visible');

        try {
            const response = await fetch(this.generateUrlValue);
            const data = await response.json();
            this.qrCodeImageTarget.src = data.qrCodeUri;
            this.authCodeSpanTarget.textContent = data.authCode;
            this.authCodeInputTarget.value = data.authCode;
        } catch (error) {
            this.close();
        }
    }

    openDisableModal(originalState) {
        this.switchTarget.dataset.originalState = originalState;
        this.disableModalTarget.classList.add('is-visible');
        this.disableModalBackdropTarget.classList.add('is-visible');
    }

    close() {
        this.switchTarget.checked = this.switchTarget.dataset.originalState === 'true';

        this.element.querySelectorAll('.modal, .modal-backdrop').forEach(el => {
            el.classList.remove('is-visible');
        });
    }
}

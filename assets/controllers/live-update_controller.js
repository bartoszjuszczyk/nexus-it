import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["conversation", "status"];
    static values = {topic: String};

    connect() {
        const url = new URL('https://127.0.0.1:9000');
        url.searchParams.append('topic', this.topicValue);
        this.eventSource = new EventSource(url);

        this.eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);

            if (data.timelineHtml) {
                this.conversationTarget.insertAdjacentHTML('afterbegin', data.timelineHtml);
            }

            if (data.type === 'status_change' && data.newStatusBadgeHtml) {
                this.statusTarget.innerHTML = data.newStatusBadgeHtml;
            }
        };
    }

    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
        }
    }
}

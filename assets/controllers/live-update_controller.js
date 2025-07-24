import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["conversation", "status"];
    static values = {topic: String};

    connect() {
        const url = new URL('https://mercure.nexus-it.test/.well-known/mercure');
        url.searchParams.append('topic', this.topicValue);
        this.eventSource = new EventSource(url);

        this.eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);

            if (data.timelineHtml) {
                this.conversationTarget.insertAdjacentHTML('afterbegin', data.timelineHtml);
            }

            if (data.type === 'status_change' && data.newStatusBadgeHtml) {
                this.statusTargets.forEach(function (el) {
                    el.innerHTML = data.newStatusBadgeHtml;
                })
            }
        };
    }

    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
        }
    }
}

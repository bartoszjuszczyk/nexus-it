import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["conversation", "status", "attachmentList"];
    static values = {
        topic: String,
        currentUserId: Number
    };

    connect() {
        this.alignExistingComments();

        const url = new URL('https://mercure.nexus-it.test/.well-known/mercure');
        url.searchParams.append('topic', this.topicValue);
        url.searchParams.append('authorization', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyIqIl19fQ.TMzyyYqIldgBLhqpiOR9a_HBk7iiP60Pb4X65ICaouA');
        this.eventSource = new EventSource(url);

        this.eventSource.onmessage = (event) => {
            const data = JSON.parse(event.data);

            if (data.type === 'status_change') {
                this.handleStatusChange(data);
            }

            if (data.type === 'new_comment' || data.type === 'new_support_comment' || data.type === 'new_internal_comment') {
                this.handleNewComment(data);
            }

            if (data.type === 'assign') {
                this.handleAssign(data);
            }

            if (data.type === 'new_attachment') {
                this.handleNewAttachment(data);
            }
        };
    }

    alignExistingComments() {
        const commentItems = this.conversationTarget.querySelectorAll('[data-author-id]');
        commentItems.forEach(element => {
            const authorId = parseInt(element.dataset.authorId, 10);
            this._alignElement(element, authorId);
        });
    }

    handleStatusChange(data) {
        const statusChangeElement = this._createTempDiv(data.timelineHtml.trim());
        this.conversationTarget.prepend(statusChangeElement);

        this.statusTargets.forEach(function (el) {
            el.innerHTML = data.newStatusBadgeHtml;
        });
    }

    handleNewComment(data) {
        const newCommentElement = this._createTempDiv(data.timelineHtml.trim());

        this._alignElement(newCommentElement, data.authorId);
        this.conversationTarget.prepend(newCommentElement);
    }

    handleAssign(data) {
        const assignElement = this._createTempDiv(data.timelineHtml.trim());
        this.conversationTarget.prepend(assignElement);
    }

    handleNewAttachment(data) {
        const attachmentElement = this._createTempDiv(data.timelineHtml.trim());
        const attachmentListHtml = this._createTempDiv(data.attachmentListHtml.trim());
        this.conversationTarget.prepend(attachmentElement);
        this.attachmentListTarget.append(attachmentListHtml);
    }

    _createTempDiv(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        return tempDiv.firstChild;
    }

    _alignElement(element, authorId) {
        if (authorId === this.currentUserIdValue) {
            element.classList.add('comment-item--current-user');
        }
    }

    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
        }
    }
}

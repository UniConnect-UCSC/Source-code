class NotificationRenderer {

    constructor(notification) {
        this.title = notification.title;
        this.message = notification.message;
        this.timestamp = notification.created_at;
        this.is_read = notification.is_read;
        this.id = notification.id;
        this.metadata = JSON.parse(notification.metadata);
        this.timeAgo = calculateTimeAgo(this.timestamp);
    }

    render() {
        throw new Error("Method 'render()' must be implemented.");
    }

    _createNewNotification() {
        const div = document.createElement('div');
        div.className = `notification-item ${this.is_read ? 'read' : 'unread'}`;
        div.setAttribute('data-id', this.id);
        return div;
    }

    _addHref() {
        const a = document.createElement('a');
        a.href = this.metadata.url || '#';
        
        a.addEventListener('click', (e) => {
            e.preventDefault(); 
            
            const notificationItem = a.closest('.notification-item');
            
            if(this.is_read === false){
                Ajax.fireAndForget('/notifications/markAsRead', this.id );
                notificationItem.classList.remove('unread');
                notificationItem.classList.add('read');
            }
            
            if (this.metadata.url && this.metadata.url !== '#') {
                window.location.href = this.metadata.url;
            }
        });
        
        return a;
    }

    _createBody(){
        const div = document.createElement('div');
        div.className = 'notification-content';
        div.innerHTML = `
            <strong class="notification-title">${this.title}</strong>
            <p class="notification-message">${this.message}</p>
            <span class="notification-time">${this.timeAgo}</span>
        `;
        return div;
    }
}

class DefaultNotificationRenderer extends NotificationRenderer {
    render() {
        const body = this._createBody();
        const notificationDiv = this._createNewNotification();
        const link = this._addHref();

        link.appendChild(body);
        notificationDiv.appendChild(link);
        return notificationDiv;
    }
}

class NotificationRouter {
    constructor() {
        this.rendererClasses = new Map();
        this.defaultClass = DefaultNotificationRenderer;
    }

    register(type, rendererClass) {
        this.rendererClasses.set(type, rendererClass);
    }

    setDefault(rendererClass) {
        this.defaultClass = rendererClass;
    }

    render(notification) {
        const RendererClass = this.rendererClasses.get(notification.type) || this.defaultClass;
        const rendererInstance = new RendererClass(notification);
        return rendererInstance.render();
    }
}

// Expose to global scope
window.NotificationRenderer = NotificationRenderer;
window.NotificationRouter = NotificationRouter;
window.DefaultNotificationRenderer = DefaultNotificationRenderer;


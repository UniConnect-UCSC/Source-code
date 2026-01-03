// Notification Management Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const sendDummyBtn = document.getElementById('sendDummyBtn');
    const dummyUserIdInput = document.getElementById('dummyUserId');
    const dummyResponse = document.getElementById('dummyResponse');

    const customForm = document.getElementById('customNotificationForm');
    const customUserIdInput = document.getElementById('customUserId');
    const notificationTypeSelect = document.getElementById('notificationType');
    const notificationTitleInput = document.getElementById('notificationTitle');
    const notificationMessageTextarea = document.getElementById('notificationMessage');
    const notificationUrlInput = document.getElementById('notificationUrl');
    const customResponse = document.getElementById('customResponse');

    // Character counter for message
    const characterCount = document.querySelector('.character-count');
    if (notificationMessageTextarea && characterCount) {
        notificationMessageTextarea.addEventListener('input', function() {
            const count = this.value.length;
            characterCount.textContent = `${count} / 500 characters`;
            
            if (count > 450) {
                characterCount.style.color = 'var(--color-error, #e74c3c)';
            } else {
                characterCount.style.color = '';
            }
        });
    }

    // Send Dummy Notification
    if (sendDummyBtn) {
        sendDummyBtn.addEventListener('click', function() {
            const userId = dummyUserIdInput.value.trim();
            
            // Disable button and show loading state
            sendDummyBtn.disabled = true;
            sendDummyBtn.innerHTML = '<span class="btn-icon">⏳</span> Sending...';
            dummyResponse.innerHTML = '';
            
            const requestData = {};
            if (userId) {
                requestData.user_id = userId;
            }

            fetch('/notifications/sendDummyNotification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showResponse(dummyResponse, 'success', 
                        `✅ Notification sent successfully!<br>
                        <strong>Title:</strong> ${data.notification.title}<br>
                        <strong>Message:</strong> ${data.notification.message}<br>
                        <strong>User ID:</strong> ${data.user_id}`
                    );
                    
                    // Clear user ID input
                    dummyUserIdInput.value = '';
                } else {
                    showResponse(dummyResponse, 'error', 
                        `❌ Error: ${data.error || 'Failed to send notification'}`
                    );
                }
            })
            .catch(error => {
                console.error('Error sending dummy notification:', error);
                showResponse(dummyResponse, 'error', 
                    `❌ Network error: ${error.message}`
                );
            })
            .finally(() => {
                // Re-enable button
                sendDummyBtn.disabled = false;
                sendDummyBtn.innerHTML = '<span class="btn-icon">📨</span> Send Random Notification';
            });
        });
    }

    // Send Custom Notification
    if (customForm) {
        customForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const userId = customUserIdInput.value.trim();
            const type = notificationTypeSelect.value;
            const title = notificationTitleInput.value.trim();
            const message = notificationMessageTextarea.value.trim();
            const url = notificationUrlInput.value.trim();

            // Validation
            if (!type || !title || !message) {
                showResponse(customResponse, 'error', 
                    '❌ Please fill in all required fields (Type, Title, and Message)'
                );
                return;
            }

            // Disable form and show loading state
            const submitBtn = customForm.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="btn-icon">⏳</span> Sending...';
            customResponse.innerHTML = '';

            const requestData = {
                type: type,
                title: title,
                message: message
            };

            if (userId) {
                requestData.user_id = userId;
            }

            if (url) {
                requestData.url = url;
            }

            fetch('/notifications/sendCustomNotification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showResponse(customResponse, 'success', 
                        `✅ ${data.message}<br>
                        <strong>User ID:</strong> ${data.user_id}<br>
                        <strong>Type:</strong> ${type}<br>
                        <strong>Title:</strong> ${title}`
                    );
                    
                    // Reset form
                    customForm.reset();
                    characterCount.textContent = '0 / 500 characters';
                    characterCount.style.color = '';
                } else {
                    showResponse(customResponse, 'error', 
                        `❌ Error: ${data.error || 'Failed to send notification'}`
                    );
                }
            })
            .catch(error => {
                console.error('Error sending custom notification:', error);
                showResponse(customResponse, 'error', 
                    `❌ Network error: ${error.message}`
                );
            })
            .finally(() => {
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span class="btn-icon">🚀</span> Send Custom Notification';
            });
        });
    }

    // Helper function to show response messages
    function showResponse(element, type, message) {
        element.className = `response-message ${type}`;
        element.innerHTML = message;
        element.style.display = 'block';
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                element.style.opacity = '0';
                setTimeout(() => {
                    element.style.display = 'none';
                    element.style.opacity = '1';
                }, 300);
            }, 5000);
        }
    }

    // Add real-time validation feedback
    if (notificationTypeSelect) {
        notificationTypeSelect.addEventListener('change', function() {
            if (this.value) {
                this.style.borderColor = 'var(--color-success, #27ae60)';
            } else {
                this.style.borderColor = '';
            }
        });
    }

    if (notificationTitleInput) {
        notificationTitleInput.addEventListener('input', function() {
            if (this.value.trim().length >= 3) {
                this.style.borderColor = 'var(--color-success, #27ae60)';
            } else {
                this.style.borderColor = '';
            }
        });
    }

    if (notificationMessageTextarea) {
        notificationMessageTextarea.addEventListener('input', function() {
            if (this.value.trim().length >= 10) {
                this.style.borderColor = 'var(--color-success, #27ae60)';
            } else {
                this.style.borderColor = '';
            }
        });
    }
});

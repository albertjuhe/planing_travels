(function() {
    'use strict';

    var chatWidget = {
        isOpen: false,
        messages: [],
        maxMessages: 100,
        unreadCount: 0,

        init: function(travelId, currentUserId, currentUsername) {
            this.travelId = travelId;
            this.currentUserId = currentUserId;
            this.currentUsername = currentUsername;
            this.renderWidget();
            this.bindEvents();
            this.setupWebSocket();
        },

        renderWidget: function() {
            var html = '\
                <div id="chat-widget" class="chat-widget">\
                    <div class="chat-widget__header" id="chat-header">\
                        <button type="button" class="chat-widget__toggle" id="chat-toggle">\
                            <svg viewBox="0 0 16 16" width="16" height="16" fill="currentColor">\
                                <path d="M2 2h12a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H6l-3 3v-3H2a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>\
                            </svg>\
                            <span class="chat-widget__toggle-label">Chat</span>\
                            <span class="chat-widget__badge" id="chat-badge" style="display:none">0</span>\
                        </button>\
                    </div>\
                    <div class="chat-widget__body" id="chat-body" style="display:none">\
                        <div class="chat-widget__messages" id="chat-messages">\
                            <div class="chat-widget__empty">No messages yet. Start the conversation!</div>\
                        </div>\
                        <div class="chat-widget__input">\
                            <input type="text" class="chat-widget__input-field" id="chat-input" placeholder="Type a message..." maxlength="1000" autocomplete="off">\
                            <button type="button" class="chat-widget__send-btn" id="chat-send">\
                                <svg viewBox="0 0 16 16" width="16" height="16" fill="currentColor">\
                                    <path d="M1 3l14 5-14 5V8l10-1L1 4V3z"/>\
                                </svg>\
                            </button>\
                        </div>\
                    </div>\
                </div>';

            $('body').append(html);
        },

        bindEvents: function() {
            var self = this;

            $('#chat-toggle').on('click', function() {
                self.toggleChat();
            });

            $('#chat-send').on('click', function() {
                self.sendMessage();
            });

            $('#chat-input').on('keypress', function(e) {
                if (e.which === 13) {
                    self.sendMessage();
                }
            });
        },

        setupWebSocket: function() {
            var self = this;

            onChatMessage(function(msg) {
                if (msg.type === 'chat') {
                    self.addMessage(msg, false);
                } else if (msg.type === 'user_joined') {
                    self.addSystemMessage(msg.username + ' joined the chat');
                } else if (msg.type === 'user_left') {
                    self.addSystemMessage(msg.username + ' left the chat');
                }
            });
        },

        toggleChat: function() {
            var self = this;
            this.isOpen = !this.isOpen;

            if (this.isOpen) {
                $('#chat-body').slideDown(200);
                this.unreadCount = 0;
                this.updateBadge();
                setTimeout(function() {
                    self.scrollToBottom();
                }, 210);
            } else {
                $('#chat-body').slideUp(200);
            }
        },

        sendMessage: function() {
            var input = $('#chat-input');
            var content = input.val().trim();

            if (!content) return;

            var msg = {
                type: 'chat',
                userId: this.currentUserId,
                username: this.currentUsername,
                content: content,
                time: new Date().toISOString()
            };

            this.addMessage(msg, true);
            sendChatMessage(content);
            input.val('');
        },

        addMessage: function(msg, isOwn) {
            var $messages = $('#chat-messages');

            if ($messages.find('.chat-widget__empty').length) {
                $messages.empty();
            }

            var time = new Date(msg.time);
            var timeStr = time.getHours().toString().padStart(2, '0') + ':' + time.getMinutes().toString().padStart(2, '0');

            var html = '\
                <div class="chat-widget__message' + (isOwn ? ' chat-widget__message--own' : '') + '">\
                    <div class="chat-widget__message-header">\
                        <span class="chat-widget__message-user">' + this.escapeHtml(msg.username) + '</span>\
                        <span class="chat-widget__message-time">' + timeStr + '</span>\
                    </div>\
                    <div class="chat-widget__message-text">' + this.escapeHtml(msg.content) + '</div>\
                </div>';

            $messages.append(html);
            this.messages.push(msg);

            if (this.messages.length > this.maxMessages) {
                $messages.find('.chat-widget__message').first().remove();
                this.messages.shift();
            }

            if (!this.isOpen && !isOwn) {
                this.unreadCount++;
                this.updateBadge();
            }

            if (this.isOpen) {
                this.scrollToBottom();
            }
        },

        addSystemMessage: function(text) {
            var $messages = $('#chat-messages');

            if ($messages.find('.chat-widget__empty').length) {
                $messages.empty();
            }

            var html = '<div class="chat-widget__system">' + this.escapeHtml(text) + '</div>';
            $messages.append(html);

            if (this.isOpen) {
                this.scrollToBottom();
            }
        },

        scrollToBottom: function() {
            var $messages = $('#chat-messages');
            $messages.scrollTop($messages[0].scrollHeight);
        },

        updateBadge: function() {
            var $badge = $('#chat-badge');
            if (this.unreadCount > 0) {
                $badge.text(this.unreadCount > 99 ? '99+' : this.unreadCount).show();
            } else {
                $badge.hide();
            }
        },

        escapeHtml: function(text) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }
    };

    window.ChatWidget = chatWidget;
})();

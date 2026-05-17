import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

window.onlineUsers = [];

document.addEventListener('DOMContentLoaded', () => {

    if (!window.Echo) {
        console.error('[Echo] Belum siap!');
        return;
    }

    console.log('[Echo] Siap. currentUserId =', window.currentUserId);

    // ================================================================
    // PRESENCE CHANNEL 'chat'
    // - Tracking online/offline semua user
    // - Listen pesan private masuk
    // - Listen typing whisper
    // ================================================================
    window.Echo.join('chat')

        .here((users) => {
            console.log('[Presence] here:', users);
            window.onlineUsers = users;
            updateAllOnlineStatus();
        })

        .joining((user) => {
            console.log('[Presence] joining:', user.name);
            if (!window.onlineUsers.find(u => u.id === user.id)) {
                window.onlineUsers.push(user);
            }
            updateAllOnlineStatus();
        })

        .leaving((user) => {
            console.log('[Presence] leaving:', user.name);
            window.onlineUsers = window.onlineUsers.filter(u => u.id !== user.id);
            updateAllOnlineStatus();
        })

        .error((err) => {
            console.error('[Presence] Error:', err);
        })

        // Pesan private masuk
        .listen('.message.sent', (e) => {
            console.log('[Private Message] Masuk:', e);
            const senderId = parseInt(e.message.sender_id);
            const selected = parseInt(window.selectedUserId);
            if (selected && senderId === selected) {
                appendMessage(e.message, false);
            }
            updateLastMessage(e.message);
        })

        // Typing indicator
        .listenForWhisper('typing', (e) => {
            const box = document.getElementById('typing-indicator');
            if (box) {
                box.textContent = `${e.name} sedang mengetik...`;
                clearTimeout(window._typingTimer);
                window._typingTimer = setTimeout(() => { box.textContent = ''; }, 2000);
            }
        });

    // ================================================================
    // PUBLIC CHANNEL 'group-chat'
    // - Listen pesan grup masuk secara realtime
    // ================================================================
    window.Echo.channel('group-chat')
        .listen('.group.message.sent', (e) => {
            console.log('[Group Message] Masuk:', e);
            appendGroupMessage(e.message);
        });

    // ================================================================
    // TYPING WHISPER — kirim saat user mengetik
    // ================================================================
    const msgInput = document.getElementById('message-input');
    if (msgInput) {
        msgInput.addEventListener('input', () => {
            window.Echo.join('chat').whisper('typing', {
                name: window.currentUserName
            });
        });
    }

    // ================================================================
    // KIRIM PESAN PRIVATE via AJAX — tanpa reload halaman
    // ================================================================
    const chatForm = document.getElementById('chat-form');
    if (chatForm) {
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(chatForm);
            try {
                const res = await fetch(chatForm.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData,
                });
                if (res.ok) {
                    const data = await res.json();
                    appendMessage(data.message, true);
                    updateLastMessage(data.message);
                    chatForm.reset();
                }
            } catch (err) {
                console.error('[AJAX] Gagal kirim pesan:', err);
            }
        });
    }

});

// ================================================================
// UPDATE SEMUA BADGE ONLINE/OFFLINE DI HALAMAN
// Bekerja di chat/index.blade.php dan groups/index.blade.php
// ================================================================
function updateAllOnlineStatus() {
    document.querySelectorAll('.online-badge[data-user-id]').forEach(el => {
        const userId = parseInt(el.dataset.userId);
        const isOnline = window.onlineUsers.some(u => parseInt(u.id) === userId);
        const offlineClass = (el.dataset.offlineClass || 'text-gray-400').split(' ');
        const onlineClass  = (el.dataset.onlineClass  || 'text-green-500 font-semibold').split(' ');
        if (isOnline) {
            el.textContent = '● Online';
            el.classList.remove(...offlineClass);
            el.classList.add(...onlineClass);
        } else {
            el.textContent = '● Offline';
            el.classList.remove(...onlineClass);
            el.classList.add(...offlineClass);
        }
    });
}

// ================================================================
// APPEND PESAN PRIVATE BARU KE CHAT BOX
// ================================================================
function appendMessage(msg, isMine) {
    const chatBox = document.getElementById('chat-box');
    if (!chatBox) return;

    const time = new Date(msg.created_at).toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit'
    });
    const fileHtml = msg.file
        ? `<div class="mt-2"><a href="/storage/${msg.file}" target="_blank"
              class="${isMine ? 'text-blue-200' : 'text-blue-600'} underline text-sm">📎 Lihat File</a></div>`
        : '';
    const html = isMine
        ? `<div class="flex justify-end">
               <div class="max-w-md">
                   <div class="bg-blue-600 text-white px-4 py-3 rounded-2xl rounded-tr-sm shadow">
                       ${msg.message ?? ''}${fileHtml}
                   </div>
                   <div class="text-right text-xs text-gray-400 mt-1">${time}</div>
               </div>
           </div>`
        : `<div class="flex justify-start">
               <div class="max-w-md">
                   <div class="bg-white px-4 py-3 rounded-2xl rounded-tl-sm shadow">
                       ${msg.message ?? ''}${fileHtml}
                   </div>
                   <div class="text-xs text-gray-400 mt-1">${time}</div>
               </div>
           </div>`;
    chatBox.insertAdjacentHTML('beforeend', html);
    chatBox.scrollTop = chatBox.scrollHeight;
}

// ================================================================
// APPEND PESAN GRUP BARU KE CHAT BOX (groups/show.blade.php)
// ================================================================
function appendGroupMessage(msg) {
    const chatBox = document.getElementById('chat-box');
    if (!chatBox) return;

    const isMine = parseInt(msg.sender_id) === parseInt(window.currentUserId);
    const time = new Date(msg.created_at).toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit'
    });
    const html = isMine
        ? `<div class="flex justify-end">
               <div class="max-w-md">
                   <div class="bg-blue-600 text-white px-4 py-3 rounded-2xl shadow">
                       <div class="text-xs opacity-70 mb-1">Kamu</div>
                       ${msg.message}
                   </div>
                   <div class="text-right text-xs text-gray-500 mt-1">${time}</div>
               </div>
           </div>`
        : `<div class="flex justify-start">
               <div class="max-w-md">
                   <div class="bg-white px-4 py-3 rounded-2xl shadow">
                       <div class="text-xs font-bold text-blue-600 mb-1">${msg.sender?.name ?? ''}</div>
                       ${msg.message}
                   </div>
                   <div class="text-xs text-gray-500 mt-1">${time}</div>
               </div>
           </div>`;
    chatBox.insertAdjacentHTML('beforeend', html);
    chatBox.scrollTop = chatBox.scrollHeight;
}

// ================================================================
// UPDATE PREVIEW PESAN TERAKHIR DI SIDEBAR
// ================================================================
function updateLastMessage(msg) {
    const otherId = parseInt(msg.sender_id) === parseInt(window.currentUserId)
        ? msg.receiver_id : msg.sender_id;
    const el = document.querySelector(`[data-last-message="${otherId}"]`);
    if (el) {
        const isMe = parseInt(msg.sender_id) === parseInt(window.currentUserId);
        el.textContent = (isMe ? 'Kamu: ' : '') + (msg.message ?? '📎 File');
    }
}
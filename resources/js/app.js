import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/*
|--------------------------------------------------------------------------
| ONLINE / OFFLINE PRESENCE
|--------------------------------------------------------------------------
*/

window.Echo.join('chat')

    .here((users) => {

        console.log('Online Users:', users);

        window.onlineUsers = users;

        updateOnlineStatus();

    })

    .joining((user) => {

        console.log(user.name + ' online');

        window.onlineUsers.push(user);

        updateOnlineStatus();

    })

    .leaving((user) => {

        console.log(user.name + ' offline');

        window.onlineUsers = window.onlineUsers.filter(
            u => u.id !== user.id
        );

        updateOnlineStatus();

    })

    /*
    |--------------------------------------------------------------------------
    | PRIVATE CHAT REALTIME
    |--------------------------------------------------------------------------
    */

    .listen('.message.sent', (e) => {

        console.log('Realtime Message:', e);

        location.reload();

    });

/*
|--------------------------------------------------------------------------
| GROUP CHAT REALTIME
|--------------------------------------------------------------------------
*/

window.Echo.channel('group-chat')

    .listen('.group.message.sent', (e) => {

        console.log('Realtime Group Message:', e);

        location.reload();

    });

/*
|--------------------------------------------------------------------------
| TYPING INDICATOR
|--------------------------------------------------------------------------
*/

window.Echo.private('chat')

    .listenForWhisper('typing', (e) => {

        const typingBox = document.getElementById('typing-indicator');

        if (typingBox) {

            typingBox.innerHTML = `
                <span class="text-sm text-gray-500 italic">
                    ${e.name} sedang mengetik...
                </span>
            `;

            setTimeout(() => {

                typingBox.innerHTML = '';

            }, 2000);

        }

    });

/*
|--------------------------------------------------------------------------
| SEND TYPING EVENT
|--------------------------------------------------------------------------
*/

const messageInput = document.getElementById('message-input');

if (messageInput) {

    messageInput.addEventListener('keydown', () => {

        window.Echo.private('chat')

            .whisper('typing', {

                name: window.currentUserName

            });

    });

}

/*
|--------------------------------------------------------------------------
| UPDATE ONLINE STATUS UI
|--------------------------------------------------------------------------
*/

function updateOnlineStatus()
{
    document.querySelectorAll('[data-user-id]').forEach(element => {

        const userId = parseInt(element.dataset.userId);

        const isOnline = window.onlineUsers.some(
            user => user.id === userId
        );

        if (isOnline) {

            element.innerHTML = `
                <span class="text-green-500 font-semibold">
                    ● Online
                </span>
            `;

        } else {

            element.innerHTML = `
                <span class="text-gray-400">
                    ● Offline
                </span>
            `;

        }

    });
}
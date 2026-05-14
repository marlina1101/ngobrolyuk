import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

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

    .listen('.message.sent', (e) => {

        console.log('Realtime Message:', e);

        location.reload();

    });

function updateOnlineStatus()
{
    document.querySelectorAll('[data-user-id]').forEach(element => {

        const userId = parseInt(element.dataset.userId);

        const isOnline = window.onlineUsers.some(
            user => user.id === userId
        );

        if(isOnline){

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
    .listen('.message.sent', (e) => {

        console.log('Realtime Message:', e);

        location.reload();

    });
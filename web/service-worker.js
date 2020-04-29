//The requested push data must be available in several event listeners
var requestedPush;

self.addEventListener('push', function(event) {

    //this code logic may change the requested push data according to browser available features
    //therefore, requested will prefix any variable containing initial sent data
    requestedPush = event.data ? event.data.json() : 'Pas de donnée transmise';
    const requestedOptions = requestedPush.payload;

    const paymentTag = 'payment';
    const registerTag = 'newpro';

    const defaultProIcon = '/bundles/cairnuser/img/pro.png';
    const defaultCairnIcon = '/bundles/cairnuser/img/favicon.png';

    if(requestedOptions.tag === paymentTag){// IF several payments, do update message in a single notification
        const promiseChain = self.registration.getNotifications()
        .then(notifications => {
            let currentNotification;

            for(let i = 0; i < notifications.length; i++) {
                if (notifications[i].tag &&
                    notifications[i].tag === paymentTag) {
                    currentNotification = notifications[i];
                }
            }

            return currentNotification;
        }).then((currentNotification) => {
            let notificationTitle;

            const newOptions = {};
            newOptions.data = requestedOptions.data;
            newOptions.icon = defaultCairnIcon;

            if (currentNotification) {
                const messageCount = currentNotification.data.newMessageCount + 1;
                // We have an open notification, let's do something with it.
                newOptions.body = `Dernier paiement : ${requestedOptions.data.amount} cairns reçus à ${requestedOptions.data.done_at} de ${requestedOptions.data.debitor} `;
                newOptions.data.newMessageCount = messageCount;

                notificationTitle = `Vous avez reçu ${messageCount} nouveaux paiements`;

                // Remember to close the old notification.
                currentNotification.close();
            } else {
                newOptions.body = requestedOptions.body;
                newOptions.data.newMessageCount = 1;
                notificationTitle = requestedPush.title;
            }

            self.registration.showNotification(notificationTitle, editOptions(newOptions));
        })
    }else if(requestedOptions.tag === registerTag){// IF several pros...
        const promiseChain = self.registration.getNotifications()
        .then(notifications => {
            let currentNotification;

            for(let i = 0; i < notifications.length; i++) {
                if (notifications[i].tag &&
                    notifications[i].tag === registerTag) {
                    currentNotification = notifications[i];
                }
            }

            return currentNotification;
        }).then((currentNotification) => {
            let notificationTitle;

            const newOptions = requestedOptions;
            newOptions.icon = defaultCairnIcon;

            if (currentNotification) {
                const messageCount = currentNotification.data.newMessageCount + 1;
                // We have an open notification, let's do something with it.
                newOptions.body = `${messageCount} nouveaux pros près de chez vous`;
                newOptions.data.newMessageCount = messageCount;
                newOptions.image = requestedOptions.image ? requestedOptions.image : defaultProIcon;

                notificationTitle = 'Le réseau du Cairn s\'agrandit !';

                // Remember to close the old notification.
                currentNotification.close();
            } else {
                newOptions.data.newMessageCount = 1;
                newOptions.image = requestedOptions.image ? requestedOptions.image : defaultProIcon;
                notificationTitle = requestedPush.title;
            }

            self.registration.showNotification(notificationTitle, editOptions(newOptions));
        })
    }else{
        event.waitUntil(
            self.registration.showNotification(requestedPush.title, editOptions(requestedOptions))
        );
    }
});


self.addEventListener('notificationclick', function(event) {
    const clickedNotification = event.notification;

    // IF actions is supported: display as an action. Otherwise, use open window on most priority action
    // IF actions is not supported, the field "action" is cleaned from the notification object once it is displayed
    // For this reason, we cannot use clickedNotification to know the initial requested behaviour regarding actions
    if ( (!("actions" in Notification)) && requestedPush.payload.actions) {
        switch(requestedPush.payload.actions[0].action){
            case 'pro-website-action':
                onProRegisterNotificationClick(clickedNotification);
                break;
            default:
                console.log('WHAT TO DO HERE ?!');
                break;
        }
    }else{//actions is supported
        if(! event.action){
            clickedNotification.close();
            return;
        }

        switch (event.action) {
            case 'pro-website-action':
                onProRegisterNotificationClick(clickedNotification);
                break;
            default:
                console.log('WHAT TO DO HERE ?!');
                break;
        }
    }
});


function onProRegisterNotificationClick(notification){
    if(! notification.data.website){
        throw new Error('no website key provided in payload data');
    }
    const proPage = notification.data.website;
    clients.openWindow(proPage);
    notification.close();
}


function editOptions(options){//edit options behaviour according to the brower available features
    //Degressive functionalities

    //FEATURE  1 : IMAGE VS ICON
    //IF there is an image & image is supported : display as an image. Otherwise, display as an icon
    if (!("image" in Notification) && options.image) {
        options.icon = options.image;
    }
    return options;
}


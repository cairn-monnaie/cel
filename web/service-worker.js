self.addEventListener('push', function(event) {
    var initPushData = event.data ? event.data.json() : 'Pas de donnée transmise';

    const paymentTag = 'push_received_paiement';
    const registerTag = 'pro_registration';

    const payload = initPushData.payload;
    const payloadTag = initPushData.tag ? initPushData.tag : 'default_tag';

    const icon = payload.icon ? payload.icon : '/bundles/cairnuser/img/favicon.png';
    const defaultProIcon = '/bundles/cairnuser/img/pro.png';

    if(payload.tag === paymentTag){// IF several payments, do update message in a single notification
        const promiseChain = self.registration.getNotifications()
        .then(notifications => {
            let currentNotification;

            for(let i = 0; i < notifications.length; i++) {
                if (notifications[i].data &&
                    notifications[i].data.tag === paymentTag) {
                    currentNotification = notifications[i];
                }
            }

            return currentNotification;
        }).then((currentNotification) => {
            let notificationTitle;

            const newOptions = {
                icon: icon,
                tag: paymentTag,
            };
            newOptions.data = payload.data;

            if (currentNotification) {
                const messageCount = currentNotification.data.newMessageCount + 1;
                // We have an open notification, let's do something with it.
                newOptions.body = `Dernier paiement : ${payload.data.amount} cairns reçus à ${payload.data.done_at} de ${payload.data.debitor} `;
                newOptions.data.newMessageCount = messageCount;
                newOptions.image = defaultProIcon;

                notificationTitle = `Vous avez reçu ${messageCount} nouveaux paiements`;

                // Remember to close the old notification.
                currentNotification.close();
            } else {
                newOptions.body = payload.body;
                newOptions.data.newMessageCount = 1;
                newOptions.image = defaultProIcon;
                notificationTitle = initPushData.title;
            }

            return self.registration.showNotification(notificationTitle, newOptions);
        })
    }else if(payload.tag === registerTag){// IF several pros...
        const promiseChain = self.registration.getNotifications()
        .then(notifications => {
            let currentNotification;

            for(let i = 0; i < notifications.length; i++) {
                if (notifications[i].data &&
                    notifications[i].data.tag === registerTag) {
                    currentNotification = notifications[i];
                }
            }

            return currentNotification;
        }).then((currentNotification) => {
            let notificationTitle;

            const newOptions = {
                icon: icon,
                tag: registerTag,
            };
            newOptions.data = payload.data;

            if (currentNotification) {
                const messageCount = currentNotification.data.newMessageCount + 1;
                // We have an open notification, let's do something with it.
                newOptions.data.body = `${messageCount} nouveaux pros près de chez vous`;
                newOptions.data.newMessageCount = messageCount;
                newOptions.image = defaultProIcon;

                notificationTitle = 'Votre réseau s\'agrandit !';

                // Remember to close the old notification.
                currentNotification.close();
            } else {
                newOptions.data.body = initPushData.body;
                newOptions.data.newMessageCount = 1;
                newOptions.image = initPushData.data.image ? initPushData.data.image : defaultProIcon;
                notificationTitle = initPushData.title;
            }

            return self.registration.showNotification(notificationTitle, newOptions);
        })
    }else{
        console.log(initPushData.payload);
        //IMAGE FIELD NOT SUPPORTED IN FIREFOX
        event.waitUntil(
            self.registration.showNotification(initPushData.title, {
                body: payload.body,
                icon: '/bundles/cairnuser/img/favicon.png'
            })
        );
    }
});


self.addEventListener('notificationclick', function(event) {
  const clickedNotification = event.notification;
  clickedNotification.close();

  // Do something as the result of the notification click
  //const promiseChain = doSomething();
  //event.waitUntil(promiseChain);
});

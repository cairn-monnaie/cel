self.addEventListener('push', function(event) {
    var data = event.data ? event.data.json() : 'Pas de donnée transmise';
    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: self.location.hostname + '/bundles/cairnuser/img/favicon.png',
        })
    );
});

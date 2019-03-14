$(document).ready(function() {
    $('select').material_select();
    $('.sidenav').hide();
    $('.sidenav-trigger').hide();
    $('.sidenav').sideNav({
        menuWidth: 300, // Default is 300
        edge: 'left', // Choose the horizontal origin
        closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
        draggable: true, // Choose whether you can drag to open on touch screens,
        onOpen: function(el) { /* Do Stuff */ }, // A function to be called when sideNav is opened
        onClose: function(el) { /* Do Stuff */ }, // A function to be called when sideNav is closed
    });
    $('.modal').modal();
    $('.tooltipped').tooltip();
    $(".dropdown-button").dropdown();

    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 2, // Creates a dropdown of 15 years to control year,
        close: 'Ok',
        closeOnSelect: true // Close upon selecting a date,
    });
});

jQuery.extend( jQuery.fn.pickadate.defaults, {
    monthsFull: [ 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre' ],
    monthsShort: [ 'Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec' ],
    weekdaysFull: [ 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi' ],
    weekdaysShort: [ 'Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam' ],
    today: 'Aujourd\'hui',
    clear: 'Effacer',
    close: 'Fermer',
    firstDay: 1,
    format: 'yyyy-mm-dd',
    formatSubmit: 'yyyy-mm-dd',
    labelMonthNext:"Mois suivant",
    labelMonthPrev:"Mois précédent",
    labelMonthSelect:"Sélectionner un mois",
    labelYearSelect:"Sélectionner une année"
});

defer(function(){
    $("[name*=\"[identityDocument][file]\"]").dropify({
        allowedFileExtensions: ['jpg','jpeg','png','pdf'],
        messages: {
            'default': '',//'Glissez déposez un fichier ici ou cliquez',
            'replace': '',//'Glissez déposez ou cliquez pour remplacer',
            'remove':  'Supprimer',
            'error':   'Oups, quelque chose a mal tourné'
        },
        error: {
            'fileSize': 'Ce fichier est trop gros ({{ value }} max).',
            'minWidth': 'L\'image n\'est pas assez large ({{ value }}}px min).',
            'maxWidth': 'L\'image est trop large ({{ value }}}px max).',
            'minHeight': 'L\'image n\'est pas haute ({{ value }}}px min).',
            'maxHeight': 'L\'image est trop haute ({{ value }}px max).',
            'imageFormat': 'Ce format n\'est pas autorisé ({{ value }} seulement).',
            'fileExtension': 'Ce format n\'est pas autorisé ({{ value }} seulement).'
        }
    });
    $("[name*=\"[image][file]\"]").dropify({
        allowedFileExtensions: ['gif','png','jpg','jpeg'],
        messages: {
            'default': '',//'Glissez déposez un fichier ici ou cliquez',
            'replace': '',//'Glissez déposez ou cliquez pour remplacer',
            'remove':  'Supprimer',
            'error':   'Oups, quelque chose a mal tourné'
        },
        error: {
            'fileSize': 'Ce fichier est trop gros ({{ value }} max).',
            'minWidth': 'L\'image n\'est pas assez large ({{ value }}}px min).',
            'maxWidth': 'L\'image est trop large ({{ value }}}px max).',
            'minHeight': 'L\'image n\'est pas haute ({{ value }}}px min).',
            'maxHeight': 'L\'image est trop haute ({{ value }}px max).',
            'imageFormat': 'Ce format n\'est pas autorisé ({{ value }} seulement).',
            'fileExtension': 'Ce format n\'est pas autorisé ({{ value }} seulement).'
        }
    });
});
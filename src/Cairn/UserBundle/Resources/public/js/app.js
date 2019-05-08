$(document).ready(function() {
    M.AutoInit();

    $('.datepicker_cairn').attr('type','text').datepicker({
        autoClose: true,
        showClearBtn: false,
        format: 'dd-mm-yyyy',
        firstDay: 1,
        i18n: {
            months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            monthsShort: ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'],
            weekdays: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
            weekdaysShort: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
            clear: 'Effacer',
            close: 'Fermer',
            cancel: 'Annuler'
        },
    });
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
        allowedFileExtensions: ['png','jpg','jpeg'],
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

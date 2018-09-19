//src/Cairn/UserBundle/Resources/views/BankConnection/envelope.js

jQuery(function($) {
    var $title = $('h3');
    var $container = $('div#cairn_userbundle_envelope_banknotes');
    var $amount_input = $('input#cairn_userbundle_envelope_amount');
    var index = $container.find(':input').length;

    //pour chaque click sur un billet, comparer la somme des billets et le montant indiquer
    $container.click(function(e) {
        compareValues();

        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });

    $('#add_banknote').click(function(e) {
        addBanknote($container);

        e.preventDefault(); // évite qu'un # apparaisse dans l'URL
        return false;
    });

    if (index == 0) {
        addBanknote($container);
    } else {
        $container.children('div').each(function() {
            addDeleteLink($(this));
        });
    }


    function compareValues(){
        var $containerValues = $('[id$="value"]');

        $providedAmount = 0;
        $totalAmount = $amount_input.val();

        $containerValues.each(function() {
            $providedAmount = parseInt($providedAmount) + parseInt($(this).val());
        });
        if($totalAmount != $providedAmount){
            $title.append('Les billets indiqués ne correspondent pas à une enveloppe de ' + $totalAmount);
        }
        else{
            $title.append('Montant valide');
        }

    }

    function addBanknote($container) {
        // Dans le contenu de l'attribut « data-prototype », on remplace :
        // - le texte "__name__label__" qu'il contient par le label du champ
        // - le texte "__name__" qu'il contient par le numéro du champ
        var template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Billet n°' + (index+1))
            .replace(/__name__/g,        index)
            ;

        // On crée un objet jquery qui contient ce template
        var $prototype = $(template);

        // On ajoute au prototype un lien pour pouvoir supprimer la catégorie
        addDeleteLink($prototype);

        // On ajoute le prototype modifié à la fin de la balise <div>
        $container.append($prototype);

        // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
        index++;
    }

    // La fonction qui ajoute un lien de suppression d'une catégorie
    function addDeleteLink($prototype) {
        // Création du lien
        var $deleteLink = $('<a href="#" class="btn btn-danger">Supprimer</a>');

        // Ajout du lien
        $prototype.append($deleteLink);

        // Ajout du listener sur le clic du lien pour effectivement supprimer la catégorie
        $deleteLink.click(function(e) {
            $prototype.remove();

            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            return false;
        });
    }
});


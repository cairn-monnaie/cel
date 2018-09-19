//src/Cairn/UserBundle/Resources/views/BankConnection/withdrawal.js

jQuery(function($) {
    var $title = $('h3');
    var $containerEnvelopes = $('div#cairn_userbundle_withdrawal_envelopes');

    var indexEnv = $containerEnvelopes.find(':input').length;
    $('#add_envelope').click(function(e) {
        addEnvelope($containerEnvelopes);

        e.preventDefault(); // évite qu'un # apparaisse dans l'url
        return false;
    });

    if (indexEnv == 0) {
        addEnvelope($containerEnvelopes);
    } else {
        $containerEnvelopes.children('div').each(function() {
            addDeleteLink($(this));
        });
    }
    //pour chaque enveloppe, on vérifie la validité des données indiquées :
    //comparaison du montant total de l'enveloppe avec les valeurs des billets renseignés
//    $containerEnvelopes.children('div').each(function() {
//        $totalAmount = $(this)
//        compareValues($(this));
//    });

    function addEnvelope($container)
    {
        //ajout de l'enveloppe avec liens de suppression et d'ajout de billet(banknote)
        var template = $container.attr('data-prototype')
            .replace(/__name__label__/g, 'Enveloppe  n°' + (indexEnv+1))
            .replace(/__name__/g,        indexEnv)
            ;
        var $prototype = $(template);

        //links
        addDeleteLink($prototype);
        addCreateBanknoteLink($prototype,indexEnv);

        $container.append($prototype);

        //        $containerTest = $('[id*="withdrawal_envelopes_0"]');
        //        console.log($containerTest);

        //gestion des billets de l'enveloppe d'indice indexEnv : comparaison des montants
        var $containerBanknotes = $('div#cairn_userbundle_withdrawal_envelopes_'+indexEnv+'_banknotes');
        var indexBan = $containerBanknotes.find(':input').length;

            $containerParent = $containerBanknotes.parent().parent().find('input[id$="amount"]');
            console.log($containerParent);

        //les évènements 
        //Evenement 1 : créer un billet quand le lien d'ajout est activé
        $('#add_banknote_' + indexEnv).click(function(e) {
            addBanknote($containerBanknotes);
            e.preventDefault(); // évite qu'un # apparaisse dans l'url
            return false;
        });
        //Evenement 2 : comparaison enveloppe/billets
        $containerBanknotes.click(function(e) {
            //RECUPERER LE NOEUD PARENT, ACCEDER AU _AMOUNT ETC
            $containerAmount = $containerBanknotes.parent().parent().find('input[id$="amount"]');
            $containerValues = $containerBanknotes.find('input[id$="value"]');
            compareValues($containerAmount, $containerValues);
        });
        if (indexBan == 0) {
            addBanknote($containerBanknotes);
        } else {
            $containerBanknotes.children('div').each(function() {
                addDeleteLink($(this));
            });
        }
        //incrémentation de l'indice de la future enveloppe
        indexEnv++;

        function addBanknote($containerBanknotes)
        {
            var template = $containerBanknotes.attr('data-prototype')
                .replace(/__name__label__/g, 'Billet n°' + (indexBan+1))
                .replace(/__name__/g,        indexBan)
                ;

            var $prototype = $(template);
            addDeleteLink($prototype);
            $containerBanknotes.append($prototype);
            indexBan++;

        }

        function compareValues($containerAmount, $containerValues){
            var totalAmount = $containerAmount.val();
            providedAmount = 0;

            $containerValues.each(function() {
                providedAmount = parseInt(providedAmount) + parseInt($(this).val());
            });
            if(totalAmount != providedAmount){
                $title.attr('#text','Montant invalide');
            }
            else{
                $title.append('Montant valide');
            }

        }



    }


    function addCreateBanknoteLink($prototype, index){
        // Création du lien
        var id = 'add_banknote_'+index;
        var $createLink = $('<a href="#" class="btn btn-default">Ajouter un billet</a>');
        $createLink.attr('id',id);
        // Ajout du lien
        $prototype.append($createLink);

        // Ajout du listener sur le clic du lien pour effectivement supprimer la catégorie
        $createLink.click(function(e) {
            $prototype.add();

            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            return false;
        });

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




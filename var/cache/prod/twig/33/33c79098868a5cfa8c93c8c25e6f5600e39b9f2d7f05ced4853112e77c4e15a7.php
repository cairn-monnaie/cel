<?php

/* CairnUserBundle:BankConnection:new_withdrawal.html.twig */
class __TwigTemplate_019d9a8e587bcbf5754236b78dd18c371c349a30b1049fc54129008a957e90ac extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserBundle:BankConnection:new_withdrawal.html.twig", 3);
        $this->blocks = array(
            'fos_user_content' => array($this, 'block_fos_user_content'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserCyclosBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_fos_user_content($context, array $blocks = array())
    {
        // line 6
        echo "
<h3>Formulaire d'enrigstrement d'un nouveau retrait</h3>

<div class=\"well\">
  ";
        // line 10
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_start', array("attr" => array("id" => "form_withdrawal")));
        echo "
  ";
        // line 11
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(twig_get_attribute($this->env, $this->source, ($context["form"] ?? null), "envelopes", array()), 'row');
        echo "
  <a href=\"#\" id=\"add_envelope\" class=\"btn btn-default\">Ajouter une enveloppe</a>

  ";
        // line 14
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_end');
        echo "
</div>
";
    }

    // line 18
    public function block_javascripts($context, array $blocks = array())
    {
        // line 19
        echo "
    <script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js\"></script>

    <script>
jQuery(function(\$) {
    var \$title = \$('h3');
    var \$containerEnvelopes = \$('div#cairn_userbundle_withdrawal_envelopes');

    var indexEnv = \$containerEnvelopes.find(':input').length;
    \$('#add_envelope').click(function(e) {
        addEnvelope(\$containerEnvelopes);

        e.preventDefault(); // évite qu'un # apparaisse dans l'url
        return false;
    });

    if (indexEnv == 0) {
        addEnvelope(\$containerEnvelopes);
    } else {
        \$containerEnvelopes.children('div').each(function() {
            addDeleteLink(\$(this));
        });
    }
    //pour chaque enveloppe, on vérifie la validité des données indiquées :
    //comparaison du montant total de l'enveloppe avec les valeurs des billets renseignés
//    \$containerEnvelopes.children('div').each(function() {
//        \$totalAmount = \$(this)
//        compareValues(\$(this));
//    });

    function addEnvelope(\$container)
    {
        //ajout de l'enveloppe avec liens de suppression et d'ajout de billet(banknote)
        var template = \$container.attr('data-prototype')
            .replace(/__name__label__/g, 'Enveloppe  n°' + (indexEnv+1))
            .replace(/__name__/g,        indexEnv)
            ;
        var \$prototype = \$(template);

        //links
        addDeleteLink(\$prototype);
        addCreateBanknoteLink(\$prototype,indexEnv);

        \$container.append(\$prototype);

        //        \$containerTest = \$('[id*=\"withdrawal_envelopes_0\"]');
        //        console.log(\$containerTest);

        //gestion des billets de l'enveloppe d'indice indexEnv : comparaison des montants
        var \$containerBanknotes = \$('div#cairn_userbundle_withdrawal_envelopes_'+indexEnv+'_banknotes');
        var indexBan = \$containerBanknotes.find(':input').length;

            \$containerParent = \$containerBanknotes.parent().parent().find('input[id\$=\"amount\"]');
            console.log(\$containerParent);

        //les évènements 
        //Evenement 1 : créer un billet quand le lien d'ajout est activé
        \$('#add_banknote_' + indexEnv).click(function(e) {
            addBanknote(\$containerBanknotes);
            e.preventDefault(); // évite qu'un # apparaisse dans l'url
            return false;
        });
        //Evenement 2 : comparaison enveloppe/billets
        \$containerBanknotes.click(function(e) {
            //RECUPERER LE NOEUD PARENT, ACCEDER AU _AMOUNT ETC
            \$containerAmount = \$containerBanknotes.parent().parent().find('input[id\$=\"amount\"]');
            \$containerValues = \$containerBanknotes.find('input[id\$=\"value\"]');
            compareValues(\$containerAmount, \$containerValues);
        });
        if (indexBan == 0) {
            addBanknote(\$containerBanknotes);
        } else {
            \$containerBanknotes.children('div').each(function() {
                addDeleteLink(\$(this));
            });
        }
        //incrémentation de l'indice de la future enveloppe
        indexEnv++;

        function addBanknote(\$containerBanknotes)
        {
            var template = \$containerBanknotes.attr('data-prototype')
                .replace(/__name__label__/g, 'Billet n°' + (indexBan+1))
                .replace(/__name__/g,        indexBan)
                ;

            var \$prototype = \$(template);
            addDeleteLink(\$prototype);
            \$containerBanknotes.append(\$prototype);
            indexBan++;

        }

        function compareValues(\$containerAmount, \$containerValues){
            var totalAmount = \$containerAmount.val();
            providedAmount = 0;

            \$containerValues.each(function() {
                providedAmount = parseInt(providedAmount) + parseInt(\$(this).val());
            });
            if(totalAmount != providedAmount){
                \$title.attr('#text','Montant invalide');
            }
            else{
                \$title.append('Montant valide');
            }

        }



    }


    function addCreateBanknoteLink(\$prototype, index){
        // Création du lien
        var id = 'add_banknote_'+index;
        var \$createLink = \$('<a href=\"#\" class=\"btn btn-default\">Ajouter un billet</a>');
        \$createLink.attr('id',id);
        // Ajout du lien
        \$prototype.append(\$createLink);

        // Ajout du listener sur le clic du lien pour effectivement supprimer la catégorie
        \$createLink.click(function(e) {
            \$prototype.add();

            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            return false;
        });

    }
    // La fonction qui ajoute un lien de suppression d'une catégorie
    function addDeleteLink(\$prototype) {
        // Création du lien
        var \$deleteLink = \$('<a href=\"#\" class=\"btn btn-danger\">Supprimer</a>');

        // Ajout du lien
        \$prototype.append(\$deleteLink);

        // Ajout du listener sur le clic du lien pour effectivement supprimer la catégorie
        \$deleteLink.click(function(e) {
            \$prototype.remove();

            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            return false;
        });
    }
});



    </script>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:BankConnection:new_withdrawal.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 19,  59 => 18,  52 => 14,  46 => 11,  42 => 10,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:BankConnection:new_withdrawal.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/BankConnection/new_withdrawal.html.twig");
    }
}

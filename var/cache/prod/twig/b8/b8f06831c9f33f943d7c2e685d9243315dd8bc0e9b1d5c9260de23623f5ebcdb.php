<?php

/* CairnUserBundle:Banking:view_recurring_transactions.html.twig */
class __TwigTemplate_1d0599ba327f19c509a510bb2d0b24f4ba90eb214526b21e3919e5ed0afb2ba5 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:view_recurring_transactions.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_body($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "

<div>
    <table>
    <caption> <span> Vos virements permanents en cours</span> </caption>
    <thead>
        <tr>
            <th> Date de demande </th>
            <th> Prochaine échéance </th>
            <th> Bénéficiaire</th>
            <th> Motif </th> 
            <th> Périodicité </th>
            <th> Montant </th>
            <th> Action </th>
 
        </tr>
    </thead>

    <tbody>
    ";
        // line 25
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["ongoingTransactions"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["transaction"]) {
            // line 26
            echo "                <tr>
                    <td> ";
            // line 27
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "date", array()), "html", null, true);
            echo " </td>
                    <td>";
            // line 28
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "nextOccurrenceDate", array()), "html", null, true);
            echo " </td>
                    <td>
                        ";
            // line 30
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "toOwner", array(), "any", false, true), "display", array(), "any", true, true)) {
                // line 31
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "toOwner", array()), "display", array()), "html", null, true);
                echo "
                        ";
            } else {
                // line 33
                echo "                            Association Le Cairn
                        ";
            }
            // line 35
            echo "                    </td>

                    <td> ";
            // line 37
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array()), "html", null, true);
            echo " </td>  
                    <td>  
                        ";
            // line 39
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "occurrenceInterval", array()), "amount", array()) == 1)) {
                // line 40
                echo "                            Mensuelle
                        ";
            } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,             // line 41
$context["transaction"], "occurrenceInterval", array()), "amount", array()) == 2)) {
                // line 42
                echo "                            Bimestrielle
                        ";
            } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,             // line 43
$context["transaction"], "occurrenceInterval", array()), "amount", array()) == 3)) {
                // line 44
                echo "                            Trimestrielle
                        ";
            } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,             // line 45
$context["transaction"], "occurrenceInterval", array()), "amount", array()) == 6)) {
                // line 46
                echo "                            Semestrielle
                        ";
            } elseif ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,             // line 47
$context["transaction"], "occurrenceInterval", array()), "amount", array()) == 12)) {
                // line 48
                echo "                            Annuelle
                        ";
            }
            // line 50
            echo "                    </td>  
                    <td> ";
            // line 51
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "currencyAmount", array()), "amount", array()), "html", null, true);
            echo " </td>
                    <td> <a href=\"";
            // line 52
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transactions_recurring_view_detailed", array("id" => twig_get_attribute($this->env, $this->source, $context["transaction"], "id", array()))), "html", null, true);
            echo "\">Voir le détail</a> | <a href=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_recurring_cancel", array("id" => twig_get_attribute($this->env, $this->source, $context["transaction"], "id", array()))), "html", null, true);
            echo "\"> Annuler </a></td>
                </tr>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 55
            echo "        Aucun virement en attente 
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transaction'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 57
        echo "    </tbody>
    </table>

    <table>
    <caption> <span> Vos virements permanents achevés</span> </caption>
    <thead>
        <tr>
            <th> Date de demande </th>
            <th> Bénéficiaire</th>
            <th> Motif </th> 
            <th> Périodicité </th>
            <th> Montant </th>
            <th> Action </th>
 
        </tr>
    </thead>

    <tbody>
    ";
        // line 75
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["processedTransactions"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["transaction"]) {
            // line 76
            echo "                <tr>
                    <td> ";
            // line 77
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "date", array()), "html", null, true);
            echo " </td>
                    <td>
                        ";
            // line 79
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "toOwner", array(), "any", false, true), "display", array(), "any", true, true)) {
                // line 80
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "toOwner", array()), "display", array()), "html", null, true);
                echo "
                        ";
            } else {
                // line 82
                echo "                            Association Le Cairn
                        ";
            }
            // line 84
            echo "                    </td>
                    <td> 
                        ";
            // line 86
            if ( !twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array(), "any", true, true)) {
                // line 87
                echo "                            Virement Cairn
                        ";
            } else {
                // line 89
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array()), "html", null, true);
                echo "
                        ";
            }
            // line 91
            echo "                    </td>  
                    <td>  
                        ";
            // line 93
            if ((twig_get_attribute($this->env, $this->source, $context["transaction"], "occurrencesCount", array()) == 1)) {
                // line 94
                echo "                            Mensuelle
                        ";
            } elseif ((twig_get_attribute($this->env, $this->source,             // line 95
$context["transaction"], "occurrencesCount", array()) == 2)) {
                // line 96
                echo "                            Bimestrielle
                        ";
            } elseif ((twig_get_attribute($this->env, $this->source,             // line 97
$context["transaction"], "occurrencesCount", array()) == 3)) {
                // line 98
                echo "                            Trimestrielle
                        ";
            } elseif ((twig_get_attribute($this->env, $this->source,             // line 99
$context["transaction"], "occurrencesCount", array()) == 6)) {
                // line 100
                echo "                            Semestrielle
                        ";
            } elseif ((twig_get_attribute($this->env, $this->source,             // line 101
$context["transaction"], "occurrencesCount", array()) == 12)) {
                // line 102
                echo "                            Annuelle
                        ";
            }
            // line 104
            echo "                    </td>  
                    <td> ";
            // line 105
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "currencyAmount", array()), "amount", array()), "html", null, true);
            echo " </td>
                    <td> <a href=\"";
            // line 106
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transactions_recurring_view_detailed", array("id" => twig_get_attribute($this->env, $this->source, $context["transaction"], "id", array()))), "html", null, true);
            echo "\">Voir le détail</a> </td>
                </tr>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 109
            echo "        Aucun virement permanent achevé 
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transaction'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 111
        echo "    </tbody>
    </table>

</div>
";
    }

    // line 117
    public function block_javascripts($context, array $blocks = array())
    {
        // line 118
        echo "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>

";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:view_recurring_transactions.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  271 => 118,  268 => 117,  260 => 111,  253 => 109,  245 => 106,  241 => 105,  238 => 104,  234 => 102,  232 => 101,  229 => 100,  227 => 99,  224 => 98,  222 => 97,  219 => 96,  217 => 95,  214 => 94,  212 => 93,  208 => 91,  202 => 89,  198 => 87,  196 => 86,  192 => 84,  188 => 82,  182 => 80,  180 => 79,  175 => 77,  172 => 76,  167 => 75,  147 => 57,  140 => 55,  130 => 52,  126 => 51,  123 => 50,  119 => 48,  117 => 47,  114 => 46,  112 => 45,  109 => 44,  107 => 43,  104 => 42,  102 => 41,  99 => 40,  97 => 39,  92 => 37,  88 => 35,  84 => 33,  78 => 31,  76 => 30,  71 => 28,  67 => 27,  64 => 26,  59 => 25,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:view_recurring_transactions.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/view_recurring_transactions.html.twig");
    }
}

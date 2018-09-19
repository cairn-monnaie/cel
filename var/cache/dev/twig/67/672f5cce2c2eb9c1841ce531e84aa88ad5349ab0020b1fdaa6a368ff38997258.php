<?php

/* CairnUserBundle:Banking:view_recurring_transactions.html.twig */
class __TwigTemplate_1a7df7ce08f49329ecb803bd7ee7728d411fa70d00295608238b630d7ffae7ab extends Twig_Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:view_recurring_transactions.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:view_recurring_transactions.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 5
    public function block_body($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

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
        $context['_seq'] = twig_ensure_traversable((isset($context["ongoingTransactions"]) || array_key_exists("ongoingTransactions", $context) ? $context["ongoingTransactions"] : (function () { throw new Twig_Error_Runtime('Variable "ongoingTransactions" does not exist.', 25, $this->source); })()));
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
        $context['_seq'] = twig_ensure_traversable((isset($context["processedTransactions"]) || array_key_exists("processedTransactions", $context) ? $context["processedTransactions"] : (function () { throw new Twig_Error_Runtime('Variable "processedTransactions" does not exist.', 75, $this->source); })()));
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
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    // line 117
    public function block_javascripts($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        // line 118
        echo "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>

";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

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
        return array (  301 => 118,  292 => 117,  278 => 111,  271 => 109,  263 => 106,  259 => 105,  256 => 104,  252 => 102,  250 => 101,  247 => 100,  245 => 99,  242 => 98,  240 => 97,  237 => 96,  235 => 95,  232 => 94,  230 => 93,  226 => 91,  220 => 89,  216 => 87,  214 => 86,  210 => 84,  206 => 82,  200 => 80,  198 => 79,  193 => 77,  190 => 76,  185 => 75,  165 => 57,  158 => 55,  148 => 52,  144 => 51,  141 => 50,  137 => 48,  135 => 47,  132 => 46,  130 => 45,  127 => 44,  125 => 43,  122 => 42,  120 => 41,  117 => 40,  115 => 39,  110 => 37,  106 => 35,  102 => 33,  96 => 31,  94 => 30,  89 => 28,  85 => 27,  82 => 26,  77 => 25,  54 => 6,  45 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/view_recurring_transactions.html.twig #}

{% extends \"CairnUserBundle::layout.html.twig\" %}

{% block body %}
    {{parent()}}

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
    {% for transaction in ongoingTransactions %}
                <tr>
                    <td> {{transaction.date}} </td>
                    <td>{{transaction.nextOccurrenceDate}} </td>
                    <td>
                        {% if transaction.toOwner.display is defined %}
                            {{transaction.toOwner.display }}
                        {% else %}
                            Association Le Cairn
                        {% endif %}
                    </td>

                    <td> {{transaction.description }} </td>  
                    <td>  
                        {% if transaction.occurrenceInterval.amount == 1 %}
                            Mensuelle
                        {% elseif transaction.occurrenceInterval.amount == 2 %}
                            Bimestrielle
                        {% elseif transaction.occurrenceInterval.amount == 3 %}
                            Trimestrielle
                        {% elseif transaction.occurrenceInterval.amount == 6 %}
                            Semestrielle
                        {% elseif transaction.occurrenceInterval.amount == 12 %}
                            Annuelle
                        {% endif %}
                    </td>  
                    <td> {{transaction.currencyAmount.amount }} </td>
                    <td> <a href=\"{{path('cairn_user_banking_transactions_recurring_view_detailed',{'id': transaction.id })}}\">Voir le détail</a> | <a href=\"{{path('cairn_user_banking_transaction_recurring_cancel', {'id' : transaction.id })}}\"> Annuler </a></td>
                </tr>
    {% else %}
        Aucun virement en attente 
    {% endfor %}
    </tbody>
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
    {% for transaction in processedTransactions %}
                <tr>
                    <td> {{transaction.date}} </td>
                    <td>
                        {% if transaction.toOwner.display is defined %}
                            {{transaction.toOwner.display }}
                        {% else %}
                            Association Le Cairn
                        {% endif %}
                    </td>
                    <td> 
                        {% if transaction.description is not defined %}
                            Virement Cairn
                        {% else %}
                            {{transaction.description}}
                        {% endif %}
                    </td>  
                    <td>  
                        {% if transaction.occurrencesCount == 1 %}
                            Mensuelle
                        {% elseif transaction.occurrencesCount == 2 %}
                            Bimestrielle
                        {% elseif transaction.occurrencesCount == 3 %}
                            Trimestrielle
                        {% elseif transaction.occurrencesCount == 6 %}
                            Semestrielle
                        {% elseif transaction.occurrencesCount == 12 %}
                            Annuelle
                        {% endif %}
                    </td>  
                    <td> {{transaction.currencyAmount.amount }} </td>
                    <td> <a href=\"{{path('cairn_user_banking_transactions_recurring_view_detailed',{'id': transaction.id })}}\">Voir le détail</a> </td>
                </tr>
    {% else %}
        Aucun virement permanent achevé 
    {% endfor %}
    </tbody>
    </table>

</div>
{% endblock %}

{% block javascripts %}
<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>

{% endblock %}
", "CairnUserBundle:Banking:view_recurring_transactions.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/view_recurring_transactions.html.twig");
    }
}

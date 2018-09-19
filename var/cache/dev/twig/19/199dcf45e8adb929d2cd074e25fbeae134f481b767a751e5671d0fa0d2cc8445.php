<?php

/* CairnUserBundle:Banking:view_single_transactions.html.twig */
class __TwigTemplate_2165710b4574266beaa791642164b01c9023f31f70432cbc7365439e015f8d03 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:view_single_transactions.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:view_single_transactions.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:view_single_transactions.html.twig"));

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
";
        // line 12
        echo "<div>
    <table>
    <caption> <span> Vos virements simples à venir </span> </caption>
    <thead>
        <tr>
            <th> Date de demande </th>
            <th> Date d'éxecution </th>
            <th> Bénéficiaire</th>
            <th> Motif </th> 
            <th> Montant </th>
            <th> Etat </th>
            <th> Action </th>
 
        </tr>
    </thead>

    <tbody>
    ";
        // line 29
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["futureInstallments"]) || array_key_exists("futureInstallments", $context) ? $context["futureInstallments"] : (function () { throw new Twig_Error_Runtime('Variable "futureInstallments" does not exist.', 29, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["installment"]) {
            // line 30
            echo "                <tr>
                    <td> ";
            // line 31
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "date", array()), "html", null, true);
            echo " </td>
                    <td> ";
            // line 32
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["installment"], "dueDate", array()), "html", null, true);
            echo " </td>
                    <td>
                        ";
            // line 34
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array(), "any", false, true), "relatedOwner", array(), "any", false, true), "display", array(), "any", true, true)) {
                // line 35
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "relatedOwner", array()), "display", array()), "html", null, true);
                echo "
                        ";
            } else {
                // line 37
                echo "                            Association Le Cairn
                        ";
            }
            // line 39
            echo "                    </td>
                    <td> ";
            // line 40
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "description", array()), "html", null, true);
            echo " </td>  
                    <td> ";
            // line 41
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["installment"], "amount", array()), "html", null, true);
            echo " </td>
                    <td> 
                        ";
            // line 43
            if ((twig_get_attribute($this->env, $this->source, $context["installment"], "status", array()) == "SCHEDULED")) {
                echo " 
                            En cours
                        ";
            } elseif ((twig_get_attribute($this->env, $this->source,             // line 45
$context["installment"], "status", array()) == "BLOCKED")) {
                // line 46
                echo "                            Bloqué
                        ";
            }
            // line 48
            echo "                    </td>
                    <td>
                        <a href=\"";
            // line 50
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transfer_view", array("type" => "scheduled.futur", "id" => twig_get_attribute($this->env, $this->source, $context["installment"], "id", array()))), "html", null, true);
            echo "\"> Voir le détail </a>
                     |
                         <a href=\"";
            // line 52
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_scheduled_changestatus", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "id", array()), "status" => "cancel")), "html", null, true);
            echo "\"> Annuler </a>
                     | 
                    ";
            // line 54
            if ((twig_get_attribute($this->env, $this->source, $context["installment"], "status", array()) == "BLOCKED")) {
                // line 55
                echo "                        <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_scheduled_changestatus", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "id", array()), "status" => "open")), "html", null, true);
                echo "\"> Débloquer </a>  </td>
                    ";
            } elseif ((twig_get_attribute($this->env, $this->source,             // line 56
$context["installment"], "status", array()) == "SCHEDULED")) {
                // line 57
                echo "                        <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_scheduled_changestatus", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["installment"], "scheduledPayment", array()), "id", array()), "status" => "block")), "html", null, true);
                echo "\"> Bloquer </a>  </td>
                    ";
            }
            // line 59
            echo "                </tr>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 61
            echo "        Aucun virement en attente 
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['installment'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 63
        echo "    </tbody>
    </table>

    <table>
    <caption> <span> Vos virements passés </span> </caption>
    <thead>
        <tr>
            <th> Date d'éxecution </th>
            <th> Bénéficiaire</th>
            <th> Motif </th> 
            <th> Montant </th>
            <th> Action </th>

        </tr>
    </thead>

    <tbody>
    ";
        // line 80
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["processedTransactions"]) || array_key_exists("processedTransactions", $context) ? $context["processedTransactions"] : (function () { throw new Twig_Error_Runtime('Variable "processedTransactions" does not exist.', 80, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["transaction"]) {
            // line 81
            echo "                <tr>
                    <td> ";
            // line 82
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "date", array()), "html", null, true);
            echo " </td>
                    <td>
                        ";
            // line 84
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "relatedOwner", array(), "any", false, true), "display", array(), "any", true, true)) {
                // line 85
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transaction"], "relatedOwner", array()), "display", array()), "html", null, true);
                echo "
                        ";
            } else {
                // line 87
                echo "                            Association Le Cairn
                        ";
            }
            // line 89
            echo "                    </td>
                    <td> ";
            // line 90
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array()), "html", null, true);
            echo " </td>  
                    <td> ";
            // line 91
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()), "html", null, true);
            echo " </td>
                    ";
            // line 92
            if (twig_get_attribute($this->env, $this->source, $context["transaction"], "processedInstallments", array(), "any", true, true)) {
                // line 93
                echo "                        <td> <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transfer_view", array("type" => "scheduled.past", "id" => twig_get_attribute($this->env, $this->source, $context["transaction"], "id", array()))), "html", null, true);
                echo "\"> Voir le détail </a></td>
                    ";
            } else {
                // line 95
                echo "                        <td> <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transfer_view", array("type" => "simple", "id" => twig_get_attribute($this->env, $this->source, $context["transaction"], "transactionNumber", array()))), "html", null, true);
                echo "\"> Voir le détail </a></td>
                    ";
            }
            // line 97
            echo "                </tr>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 99
            echo "        Aucun virement effectué 
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transaction'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 101
        echo "    </tbody>
    </table>

</div>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:view_single_transactions.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  253 => 101,  246 => 99,  240 => 97,  234 => 95,  228 => 93,  226 => 92,  222 => 91,  218 => 90,  215 => 89,  211 => 87,  205 => 85,  203 => 84,  198 => 82,  195 => 81,  190 => 80,  171 => 63,  164 => 61,  158 => 59,  152 => 57,  150 => 56,  145 => 55,  143 => 54,  138 => 52,  133 => 50,  129 => 48,  125 => 46,  123 => 45,  118 => 43,  113 => 41,  109 => 40,  106 => 39,  102 => 37,  96 => 35,  94 => 34,  89 => 32,  85 => 31,  82 => 30,  77 => 29,  58 => 12,  53 => 6,  44 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/view_single_transactions.html.twig #}

{% extends \"CairnUserBundle::layout.html.twig\" %}

{% block body %}
    {{parent()}}
{#<div class=\"well\">
  {{ form_start(form) }}
  {{ form_rest(form) }}
  {{form_end(form) }}
</div>#}
<div>
    <table>
    <caption> <span> Vos virements simples à venir </span> </caption>
    <thead>
        <tr>
            <th> Date de demande </th>
            <th> Date d'éxecution </th>
            <th> Bénéficiaire</th>
            <th> Motif </th> 
            <th> Montant </th>
            <th> Etat </th>
            <th> Action </th>
 
        </tr>
    </thead>

    <tbody>
    {% for installment in futureInstallments %}
                <tr>
                    <td> {{installment.scheduledPayment.date}} </td>
                    <td> {{installment.dueDate}} </td>
                    <td>
                        {% if installment.scheduledPayment.relatedOwner.display is defined %}
                            {{installment.scheduledPayment.relatedOwner.display}}
                        {% else %}
                            Association Le Cairn
                        {% endif %}
                    </td>
                    <td> {{installment.scheduledPayment.description }} </td>  
                    <td> {{installment.amount }} </td>
                    <td> 
                        {% if installment.status == 'SCHEDULED' %} 
                            En cours
                        {% elseif installment.status == 'BLOCKED' %}
                            Bloqué
                        {% endif %}
                    </td>
                    <td>
                        <a href=\"{{path('cairn_user_banking_transfer_view', {'type':'scheduled.futur','id' : installment.id})}}\"> Voir le détail </a>
                     |
                         <a href=\"{{path('cairn_user_banking_transaction_scheduled_changestatus', {'id' : installment.scheduledPayment.id,'status' : 'cancel' })}}\"> Annuler </a>
                     | 
                    {% if installment.status == 'BLOCKED' %}
                        <a href=\"{{path('cairn_user_banking_transaction_scheduled_changestatus', {'id' : installment.scheduledPayment.id, 'status':'open' })}}\"> Débloquer </a>  </td>
                    {% elseif  installment.status == 'SCHEDULED' %}
                        <a href=\"{{path('cairn_user_banking_transaction_scheduled_changestatus', {'id' : installment.scheduledPayment.id, 'status':'block' })}}\"> Bloquer </a>  </td>
                    {% endif %}
                </tr>
    {% else %}
        Aucun virement en attente 
    {% endfor %}
    </tbody>
    </table>

    <table>
    <caption> <span> Vos virements passés </span> </caption>
    <thead>
        <tr>
            <th> Date d'éxecution </th>
            <th> Bénéficiaire</th>
            <th> Motif </th> 
            <th> Montant </th>
            <th> Action </th>

        </tr>
    </thead>

    <tbody>
    {% for transaction in processedTransactions %}
                <tr>
                    <td> {{transaction.date}} </td>
                    <td>
                        {% if transaction.relatedOwner.display is defined %}
                            {{transaction.relatedOwner.display}}
                        {% else %}
                            Association Le Cairn
                        {% endif %}
                    </td>
                    <td> {{transaction.description }} </td>  
                    <td> {{transaction.amount }} </td>
                    {% if transaction.processedInstallments is defined %}
                        <td> <a href=\"{{path('cairn_user_banking_transfer_view', {'type':'scheduled.past','id' : transaction.id})}}\"> Voir le détail </a></td>
                    {% else %}
                        <td> <a href=\"{{path('cairn_user_banking_transfer_view', {'type':'simple','id' : transaction.transactionNumber})}}\"> Voir le détail </a></td>
                    {% endif %}
                </tr>
    {% else %}
        Aucun virement effectué 
    {% endfor %}
    </tbody>
    </table>

</div>
{% endblock %}

", "CairnUserBundle:Banking:view_single_transactions.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/view_single_transactions.html.twig");
    }
}

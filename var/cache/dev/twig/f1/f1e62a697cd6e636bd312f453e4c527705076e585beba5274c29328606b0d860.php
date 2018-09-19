<?php

/* CairnUserBundle:Banking:view_detailed_recurring_transactions.html.twig */
class __TwigTemplate_9482ee40204761d5ac43781a9aacdf688a830611f41a6ee94c2052b5c647b70a extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:view_detailed_recurring_transactions.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:view_detailed_recurring_transactions.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:view_detailed_recurring_transactions.html.twig"));

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
        <caption> <span> Détail du virement permanent</span> </caption>
        <thead>
            <tr>
                <th> N° </th>
                <th> Montant </th>
                <th> Date d'éxecution</th>
                <th> Etat </th>
                <th> Action </th> 
            </tr>
        </thead>
    
        <tbody>
        ";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, (isset($context["data"]) || array_key_exists("data", $context) ? $context["data"] : (function () { throw new Twig_Error_Runtime('Variable "data" does not exist.', 21, $this->source); })()), "occurrences", array()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["occurrence"]) {
            // line 22
            echo "                    <tr>
                        <td> ";
            // line 23
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["occurrence"], "number", array()), "html", null, true);
            echo " </td>
                        <td>";
            // line 24
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["occurrence"], "currencyAmount", array()), "amount", array()), "html", null, true);
            echo " </td>
                        <td> ";
            // line 25
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["occurrence"], "date", array()), "html", null, true);
            echo " </td>  
                        <td> ";
            // line 26
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["occurrence"], "status", array()), "html", null, true);
            echo " </td>
                        <td>
                             ";
            // line 28
            if ((twig_get_attribute($this->env, $this->source, $context["occurrence"], "status", array()) == "FAILED")) {
                // line 29
                echo "                                <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_occurrence_execute", array("id" => twig_get_attribute($this->env, $this->source, $context["occurrence"], "id", array()))), "html", null, true);
                echo "\" >Executer </a>
                             ";
            } else {
                // line 31
                echo "                                <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transfer_view", array("type" => "recurring", "id" => twig_get_attribute($this->env, $this->source, $context["occurrence"], "id", array()))), "html", null, true);
                echo "\"> Voir le détail </a>
                             ";
            }
            // line 33
            echo "                        </td>
                    </tr>
        ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 36
            echo "            Aucune occurrence executée
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['occurrence'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 38
        echo "        </tbody>
        </table>
    </div>    
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:view_detailed_recurring_transactions.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  125 => 38,  118 => 36,  111 => 33,  105 => 31,  99 => 29,  97 => 28,  92 => 26,  88 => 25,  84 => 24,  80 => 23,  77 => 22,  72 => 21,  53 => 6,  44 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/view_detailed_recurring_transactions.html.twig #}

{% extends \"CairnUserBundle::layout.html.twig\" %}

{% block body %}
    {{parent()}}
    <div>
        <table>
        <caption> <span> Détail du virement permanent</span> </caption>
        <thead>
            <tr>
                <th> N° </th>
                <th> Montant </th>
                <th> Date d'éxecution</th>
                <th> Etat </th>
                <th> Action </th> 
            </tr>
        </thead>
    
        <tbody>
        {% for occurrence in data.occurrences %}
                    <tr>
                        <td> {{occurrence.number}} </td>
                        <td>{{occurrence.currencyAmount.amount}} </td>
                        <td> {{occurrence.date }} </td>  
                        <td> {{occurrence.status }} </td>
                        <td>
                             {% if occurrence.status == 'FAILED' %}
                                <a href=\"{{path('cairn_user_banking_transaction_occurrence_execute',{'id': occurrence.id })}}\" >Executer </a>
                             {% else %}
                                <a href=\"{{path('cairn_user_banking_transfer_view', {'type':'recurring','id' : occurrence.id})}}\"> Voir le détail </a>
                             {% endif %}
                        </td>
                    </tr>
        {% else %}
            Aucune occurrence executée
        {% endfor %}
        </tbody>
        </table>
    </div>    
{% endblock %}
", "CairnUserBundle:Banking:view_detailed_recurring_transactions.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/view_detailed_recurring_transactions.html.twig");
    }
}

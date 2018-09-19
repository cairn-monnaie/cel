<?php

/* CairnUserBundle:Pdf:accounts_statement.html.twig */
class __TwigTemplate_7f25810cc24089c6a55e11f03903cd9aa2480ab8c9f08573c9a87d7371359bf9 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout-pdf.html.twig", "CairnUserBundle:Pdf:accounts_statement.html.twig", 3);
        $this->blocks = array(
            'fos_user_content' => array($this, 'block_fos_user_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserBundle::layout-pdf.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Pdf:accounts_statement.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Pdf:accounts_statement.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 6
    public function block_fos_user_content($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "fos_user_content"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "fos_user_content"));

        // line 7
        echo "    ";
        $context["transactions"] = twig_get_attribute($this->env, $this->source, (isset($context["history"]) || array_key_exists("history", $context) ? $context["history"] : (function () { throw new Twig_Error_Runtime('Variable "history" does not exist.', 7, $this->source); })()), "transactions", array());
        // line 8
        echo "    ";
        $context["status"] = twig_get_attribute($this->env, $this->source, (isset($context["history"]) || array_key_exists("history", $context) ? $context["history"] : (function () { throw new Twig_Error_Runtime('Variable "history" does not exist.', 8, $this->source); })()), "status", array());
        // line 9
        echo "    ";
        $context["currency"] = twig_get_attribute($this->env, $this->source, (isset($context["account"]) || array_key_exists("account", $context) ? $context["account"] : (function () { throw new Twig_Error_Runtime('Variable "account" does not exist.', 9, $this->source); })()), "currency", array());
        // line 10
        echo "    ";
        $context["balance"] = twig_get_attribute($this->env, $this->source, (isset($context["status"]) || array_key_exists("status", $context) ? $context["status"] : (function () { throw new Twig_Error_Runtime('Variable "status" does not exist.', 10, $this->source); })()), "balanceAtBegin", array());
        // line 11
        echo "
    <table>
        <caption> Situation de votre compte ";
        // line 13
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["account"]) || array_key_exists("account", $context) ? $context["account"] : (function () { throw new Twig_Error_Runtime('Variable "account" does not exist.', 13, $this->source); })()), "type", array()), "name", array()), "html", null, true);
        echo "
            ";
        // line 14
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "owner", array(), "any", false, true), "display", array(), "any", true, true)) {
            // line 15
            echo "                ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["account"]) || array_key_exists("account", $context) ? $context["account"] : (function () { throw new Twig_Error_Runtime('Variable "account" does not exist.', 15, $this->source); })()), "owner", array()), "display", array()), "html", null, true);
            echo "
            ";
        } else {
            // line 17
            echo "                Association Le Cairn 
            ";
        }
        // line 19
        echo "             (";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["currency"]) || array_key_exists("currency", $context) ? $context["currency"] : (function () { throw new Twig_Error_Runtime('Variable "currency" does not exist.', 19, $this->source); })()), "name", array()), "html", null, true);
        echo ") au ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["period"]) || array_key_exists("period", $context) ? $context["period"] : (function () { throw new Twig_Error_Runtime('Variable "period" does not exist.', 19, $this->source); })()), "end", array()), "html", null, true);
        echo "         </br>             RIB Cairn : ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["account"]) || array_key_exists("account", $context) ? $context["account"] : (function () { throw new Twig_Error_Runtime('Variable "account" does not exist.', 19, $this->source); })()), "id", array()), "html", null, true);
        echo "
       </caption>
        <tr> Solde au ";
        // line 21
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["period"]) || array_key_exists("period", $context) ? $context["period"] : (function () { throw new Twig_Error_Runtime('Variable "period" does not exist.', 21, $this->source); })()), "begin", array()), "html", null, true);
        echo " : ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["status"]) || array_key_exists("status", $context) ? $context["status"] : (function () { throw new Twig_Error_Runtime('Variable "status" does not exist.', 21, $this->source); })()), "balanceAtBegin", array()), "html", null, true);
        echo " </tr>
        <thead>
            <tr>
                <th> Date de valeur </th>
                <th> Description </th>
                <th> Débit </th>
                <th> Crédit </th> 
                <th> Solde </th> 
    
            </tr>
        </thead>
    
        <tbody>
            ";
        // line 34
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["transactions"]) || array_key_exists("transactions", $context) ? $context["transactions"] : (function () { throw new Twig_Error_Runtime('Variable "transactions" does not exist.', 34, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["transaction"]) {
            // line 35
            echo "                ";
            $context["balance"] = ((isset($context["balance"]) || array_key_exists("balance", $context) ? $context["balance"] : (function () { throw new Twig_Error_Runtime('Variable "balance" does not exist.', 35, $this->source); })()) + twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()));
            // line 36
            echo "                <tr>
                    <td> ";
            // line 37
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "date", array()), "html", null, true);
            echo " </td>
                    <td> ";
            // line 38
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array()), "html", null, true);
            echo " </td>
                    <td> 
                        ";
            // line 40
            if ((twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()) < 0)) {
                // line 41
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()), "html", null, true);
                echo " ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["currency"]) || array_key_exists("currency", $context) ? $context["currency"] : (function () { throw new Twig_Error_Runtime('Variable "currency" does not exist.', 41, $this->source); })()), "suffix", array()), "html", null, true);
                echo "
                        ";
            }
            // line 43
            echo "                    </td>
                    <td> 
                        ";
            // line 45
            if ((twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()) > 0)) {
                // line 46
                echo "                            ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()), "html", null, true);
                echo " ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["currency"]) || array_key_exists("currency", $context) ? $context["currency"] : (function () { throw new Twig_Error_Runtime('Variable "currency" does not exist.', 46, $this->source); })()), "suffix", array()), "html", null, true);
                echo "
                        ";
            }
            // line 48
            echo "                    </td>

                    <td> 
                        ";
            // line 51
            echo twig_escape_filter($this->env, (isset($context["balance"]) || array_key_exists("balance", $context) ? $context["balance"] : (function () { throw new Twig_Error_Runtime('Variable "balance" does not exist.', 51, $this->source); })()), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["currency"]) || array_key_exists("currency", $context) ? $context["currency"] : (function () { throw new Twig_Error_Runtime('Variable "currency" does not exist.', 51, $this->source); })()), "suffix", array()), "html", null, true);
            echo "
                    </td>

                </tr>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transaction'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 56
        echo "        </tbody>

        <tr> Solde au ";
        // line 58
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["period"]) || array_key_exists("period", $context) ? $context["period"] : (function () { throw new Twig_Error_Runtime('Variable "period" does not exist.', 58, $this->source); })()), "end", array()), "html", null, true);
        echo " : ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["status"]) || array_key_exists("status", $context) ? $context["status"] : (function () { throw new Twig_Error_Runtime('Variable "status" does not exist.', 58, $this->source); })()), "balanceAtEnd", array()), "html", null, true);
        echo " </tr>

    </table>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Pdf:accounts_statement.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  178 => 58,  174 => 56,  161 => 51,  156 => 48,  148 => 46,  146 => 45,  142 => 43,  134 => 41,  132 => 40,  127 => 38,  123 => 37,  120 => 36,  117 => 35,  113 => 34,  95 => 21,  85 => 19,  81 => 17,  75 => 15,  73 => 14,  69 => 13,  65 => 11,  62 => 10,  59 => 9,  56 => 8,  53 => 7,  44 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Pdf/accounts_statement.html.twig #}         

{% extends 'CairnUserBundle::layout-pdf.html.twig' %}


{% block fos_user_content %}
    {% set transactions = history.transactions %}
    {% set status = history.status %}
    {% set currency = account.currency %}
    {% set balance = status.balanceAtBegin %}

    <table>
        <caption> Situation de votre compte {{account.type.name}}
            {% if account.owner.display is defined %}
                {{account.owner.display}}
            {% else %}
                Association Le Cairn 
            {% endif %}
             ({{currency.name}}) au {{period.end}}         </br>             RIB Cairn : {{account.id}}
       </caption>
        <tr> Solde au {{period.begin}} : {{status.balanceAtBegin}} </tr>
        <thead>
            <tr>
                <th> Date de valeur </th>
                <th> Description </th>
                <th> Débit </th>
                <th> Crédit </th> 
                <th> Solde </th> 
    
            </tr>
        </thead>
    
        <tbody>
            {% for transaction in transactions %}
                {% set balance = balance + transaction.amount %}
                <tr>
                    <td> {{transaction.date}} </td>
                    <td> {{transaction.description}} </td>
                    <td> 
                        {% if transaction.amount < 0 %}
                            {{transaction.amount}} {{currency.suffix}}
                        {% endif %}
                    </td>
                    <td> 
                        {% if transaction.amount > 0 %}
                            {{transaction.amount}} {{currency.suffix}}
                        {% endif %}
                    </td>

                    <td> 
                        {{ balance}} {{currency.suffix}}
                    </td>

                </tr>
            {% endfor %}
        </tbody>

        <tr> Solde au {{period.end}} : {{status.balanceAtEnd}} </tr>

    </table>
{% endblock %}

", "CairnUserBundle:Pdf:accounts_statement.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pdf/accounts_statement.html.twig");
    }
}

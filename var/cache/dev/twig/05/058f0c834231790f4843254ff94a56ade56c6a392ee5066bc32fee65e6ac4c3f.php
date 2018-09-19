<?php

/* CairnUserBundle:Banking:accounts_table.html.twig */
class __TwigTemplate_ed2dbc2b6f177b810b0352d3a90e344216ba48fa44853cc7b8e9a5d767674de9 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:accounts_table.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:accounts_table.html.twig"));

        // line 2
        echo "
    <table>
    <caption>  Les comptes  </caption>
    <thead>
        <tr>
            <th> Nom du compte </th>
            <th> Identifiant </th>
            <th> Solde </th>
            <th> Capacité de dépense </th> 
            <th> Action </th> 

        </tr>
    </thead>

    <tbody>
        ";
        // line 17
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["accounts"]) || array_key_exists("accounts", $context) ? $context["accounts"] : (function () { throw new Twig_Error_Runtime('Variable "accounts" does not exist.', 17, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["account"]) {
            // line 18
            echo "            <tr>
                <td>  
                    <a href=\"";
            // line 20
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_account_operations", array("accountID" => twig_get_attribute($this->env, $this->source, $context["account"], "id", array()))), "html", null, true);
            echo "\"> ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "type", array()), "name", array()), "html", null, true);
            echo " </a>
                </td>
                <td> ";
            // line 22
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "id", array()), "html", null, true);
            echo " </td>
                <td> ";
            // line 23
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "status", array()), "balance", array()), "html", null, true);
            echo " </td>
                <td> 
                    ";
            // line 25
            if (twig_get_attribute($this->env, $this->source, $context["account"], "unlimited", array())) {
                // line 26
                echo "                        infinie
                    ";
            } else {
                // line 28
                echo "                        ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "status", array()), "availableBalance", array()), "html", null, true);
                echo " 
                    ";
            }
            // line 30
            echo "                </td>
                <td>
                    ";
            // line 32
            if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
                // line 33
                echo "                        ";
                if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "type", array()), "nature", array()) == "USER")) {
                    // line 34
                    echo "                            <a href=\"";
                    echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_account_edit", array("id" => twig_get_attribute($this->env, $this->source, $context["account"], "id", array()))), "html", null, true);
                    echo "\"> Modifier </a>
                        ";
                }
                // line 36
                echo "                    ";
            } else {
                // line 37
                echo "                        <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_account_operations", array("accountID" => twig_get_attribute($this->env, $this->source, $context["account"], "id", array()))), "html", null, true);
                echo "\"> Voir </a>
                    ";
            }
            // line 39
            echo "                </td>
            </tr>
        ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 42
            echo "            Pas de compte ouvert
        ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['account'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 44
        echo "    </tbody>

    </table> 
";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:accounts_table.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  121 => 44,  114 => 42,  107 => 39,  101 => 37,  98 => 36,  92 => 34,  89 => 33,  87 => 32,  83 => 30,  77 => 28,  73 => 26,  71 => 25,  66 => 23,  62 => 22,  55 => 20,  51 => 18,  46 => 17,  29 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/accounts_table.html.twig #}

    <table>
    <caption>  Les comptes  </caption>
    <thead>
        <tr>
            <th> Nom du compte </th>
            <th> Identifiant </th>
            <th> Solde </th>
            <th> Capacité de dépense </th> 
            <th> Action </th> 

        </tr>
    </thead>

    <tbody>
        {% for account in accounts %}
            <tr>
                <td>  
                    <a href=\"{{path('cairn_user_banking_account_operations',{'accountID':account.id}) }}\"> {{account.type.name}} </a>
                </td>
                <td> {{account.id}} </td>
                <td> {{account.status.balance}} </td>
                <td> 
                    {% if account.unlimited %}
                        infinie
                    {% else %}
                        {{account.status.availableBalance}} 
                    {% endif %}
                </td>
                <td>
                    {% if is_granted('ROLE_SUPER_ADMIN') %}
                        {% if account.type.nature == 'USER' %}
                            <a href=\"{{path('cairn_user_cyclos_accountsconfig_account_edit',{'id':account.id})  }}\"> Modifier </a>
                        {% endif %}
                    {% else %}
                        <a href=\"{{path('cairn_user_banking_account_operations',{'accountID':account.id})}}\"> Voir </a>
                    {% endif %}
                </td>
            </tr>
        {% else %}
            Pas de compte ouvert
        {% endfor %}
    </tbody>

    </table> 
", "CairnUserBundle:Banking:accounts_table.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/accounts_table.html.twig");
    }
}

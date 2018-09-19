<?php

/* CairnUserCyclosBundle:Config/AccountType:view.html.twig */
class __TwigTemplate_7314f6e3f094e19c1c3b086aac1d5c155dd7dbd28bfa012853455743728dad93 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/AccountType:view.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserCyclosBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserCyclosBundle:Config/AccountType:view.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserCyclosBundle:Config/AccountType:view.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 6
    public function block_body($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 7
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "
    
    <h2> ";
        // line 9
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 9, $this->source); })()), "name", array()), "html", null, true);
        echo " </h2>
  <div class=\"well\">
    <div>
        ";
        // line 12
        if ((twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 12, $this->source); })()), "nature", array()) == "USER")) {
            // line 13
            echo "            <li> Limite basse : ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new Twig_Error_Runtime('Variable "product" does not exist.', 13, $this->source); })()), "defaultCreditLimit", array()), "html", null, true);
            echo "</li>
            <li> Etat :
                ";
            // line 15
            if ((isset($context["isAssigned"]) || array_key_exists("isAssigned", $context) ? $context["isAssigned"] : (function () { throw new Twig_Error_Runtime('Variable "isAssigned" does not exist.', 15, $this->source); })())) {
                // line 16
                echo "                      Ouvert
                ";
            } else {
                // line 18
                echo "                      Fermé
                ";
            }
            // line 20
            echo "
            </li>
         ";
            // line 22
            echo " 

        ";
        } else {
            // line 25
            echo "            <li>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 25, $this->source); })()), "limitType", array()), "html", null, true);
            echo "</li>
            ";
            // line 26
            if ((twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 26, $this->source); })()), "limitType", array()) == "LIMITED")) {
                // line 27
                echo "                <li>";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 27, $this->source); })()), "creditLimit", array()), "html", null, true);
                echo "</li>
            ";
            }
            // line 29
            echo "        ";
        }
        // line 30
        echo "    </div>

        <h3> Types de transfert liés au compte</h3>
            <h4> Transferts vers types de comptes professionnels </h4>
                <table>
                    <tr>
                        <th>Vers le compte</th>
                        <th>Actif</th>
                      ";
        // line 39
        echo "                        <th>Action</th>

                    </tr>
                    ";
        // line 42
        if ((twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 42, $this->source); })()), "nature", array()) == "USER")) {
            // line 43
            echo "                        ";
            $context["listTransferTypes"] = twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new Twig_Error_Runtime('Variable "product" does not exist.', 43, $this->source); })()), "userPayments", array());
            // line 44
            echo "                    ";
        } else {
            // line 45
            echo "                        ";
            $context["listTransferTypes"] = twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new Twig_Error_Runtime('Variable "product" does not exist.', 45, $this->source); })()), "systemToUserPayments", array());
            // line 46
            echo "                    ";
        }
        // line 47
        echo "                    ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["listTransferTypes"]) || array_key_exists("listTransferTypes", $context) ? $context["listTransferTypes"] : (function () { throw new Twig_Error_Runtime('Variable "listTransferTypes" does not exist.', 47, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["transferType"]) {
            // line 48
            echo "                        ";
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transferType"], "from", array()), "name", array()) == twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 48, $this->source); })()), "name", array()))) {
                // line 49
                echo "
                            <tr>
                                <td><a href=\"";
                // line 51
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_view", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transferType"], "to", array()), "id", array()))), "html", null, true);
                echo "\"> ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transferType"], "to", array()), "name", array()), "html", null, true);
                echo "</a></td>
                                <td> ";
                // line 52
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transferType"], "enabled", array()), "html", null, true);
                echo " </td>
                              ";
                // line 54
                echo "                                <td><a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_transfertype_view", array("id" => twig_get_attribute($this->env, $this->source, $context["transferType"], "id", array()))), "html", null, true);
                echo "\">Voir</a></td>
    
                                </a>
                            </tr>
                        ";
            }
            // line 59
            echo "                    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 60
            echo "                        Pas de type de transfert associé
                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transferType'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 62
        echo "                </table>

             <h4> Transferts vers types de comptes système </h4>
                <table>
                    <tr>
                        <th>Vers le compte</th>
                        <th>Actif</th>
                      ";
        // line 70
        echo "                    </tr>
                    ";
        // line 71
        if ((twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 71, $this->source); })()), "nature", array()) == "USER")) {
            // line 72
            echo "                        ";
            $context["listTransferTypes"] = twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new Twig_Error_Runtime('Variable "product" does not exist.', 72, $this->source); })()), "systemPayments", array());
            // line 73
            echo "                    ";
        } else {
            // line 74
            echo "                        ";
            $context["listTransferTypes"] = twig_get_attribute($this->env, $this->source, (isset($context["product"]) || array_key_exists("product", $context) ? $context["product"] : (function () { throw new Twig_Error_Runtime('Variable "product" does not exist.', 74, $this->source); })()), "systemToSystemPayments", array());
            // line 75
            echo "                    ";
        }
        // line 76
        echo "
                    ";
        // line 77
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["listTransferTypes"]) || array_key_exists("listTransferTypes", $context) ? $context["listTransferTypes"] : (function () { throw new Twig_Error_Runtime('Variable "listTransferTypes" does not exist.', 77, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["transferType"]) {
            // line 78
            echo "                        ";
            if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transferType"], "from", array()), "name", array()) == twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 78, $this->source); })()), "name", array()))) {
                // line 79
                echo "                            <tr>
                                <td><a href=\"";
                // line 80
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_view", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transferType"], "to", array()), "id", array()))), "html", null, true);
                echo "\"> ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["transferType"], "to", array()), "name", array()), "html", null, true);
                echo "</a></td>
                                <td> ";
                // line 81
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transferType"], "enabled", array()), "html", null, true);
                echo " </td>
                               ";
                // line 83
                echo "                                <td><a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_transfertype_view", array("id" => twig_get_attribute($this->env, $this->source, $context["transferType"], "id", array()))), "html", null, true);
                echo "\">Voir</a></td>
                            </tr>
                        ";
            }
            // line 86
            echo "                    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transferType'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 87
        echo "
                </table>
  </div>

  <p>
    <a href=\"";
        // line 92
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_list");
        echo "\">
      Retour à la liste des comptes
    </a>
     <a href=\"";
        // line 95
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_edit", array("id" => twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 95, $this->source); })()), "id", array()))), "html", null, true);
        echo "\">
      Mettre à jour le type de compte
    </a>
    ";
        // line 98
        if ((twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 98, $this->source); })()), "nature", array()) == "USER")) {
            // line 99
            echo "        ";
            if ((isset($context["isAssigned"]) || array_key_exists("isAssigned", $context) ? $context["isAssigned"] : (function () { throw new Twig_Error_Runtime('Variable "isAssigned" does not exist.', 99, $this->source); })())) {
                // line 100
                echo "             <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_remove_confirm", array("id" => twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 100, $this->source); })()), "id", array()))), "html", null, true);
                echo "\">
              Fermer le type de compte
             </a>

        ";
            } else {
                // line 105
                echo "             <a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_open_confirm", array("id" => twig_get_attribute($this->env, $this->source, (isset($context["accountType"]) || array_key_exists("accountType", $context) ? $context["accountType"] : (function () { throw new Twig_Error_Runtime('Variable "accountType" does not exist.', 105, $this->source); })()), "id", array()))), "html", null, true);
                echo "\">
              Ouvrir le type de compte
            </a>

        ";
            }
            // line 110
            echo "    ";
        }
        // line 111
        echo "
  </p>

";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/AccountType:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  288 => 111,  285 => 110,  276 => 105,  267 => 100,  264 => 99,  262 => 98,  256 => 95,  250 => 92,  243 => 87,  237 => 86,  230 => 83,  226 => 81,  220 => 80,  217 => 79,  214 => 78,  210 => 77,  207 => 76,  204 => 75,  201 => 74,  198 => 73,  195 => 72,  193 => 71,  190 => 70,  181 => 62,  174 => 60,  169 => 59,  160 => 54,  156 => 52,  150 => 51,  146 => 49,  143 => 48,  137 => 47,  134 => 46,  131 => 45,  128 => 44,  125 => 43,  123 => 42,  118 => 39,  108 => 30,  105 => 29,  99 => 27,  97 => 26,  92 => 25,  87 => 22,  83 => 20,  79 => 18,  75 => 16,  73 => 15,  67 => 13,  65 => 12,  59 => 9,  53 => 7,  44 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserCyclosBundle/Resources/views/Config/AccountType/view.html.twig #}

{% extends \"CairnUserCyclosBundle::layout.html.twig\" %}


{% block body %}
    {{parent()}}
    
    <h2> {{accountType.name}} </h2>
  <div class=\"well\">
    <div>
        {% if accountType.nature == 'USER' %}
            <li> Limite basse : {{ product.defaultCreditLimit }}</li>
            <li> Etat :
                {% if isAssigned %}
                      Ouvert
                {% else %}
                      Fermé
                {% endif %}

            </li>
         {#   <li> href=\"{{ path('cairn_user_cyclos_accountsconfig_accounttype_users', {'id' : accountType.id}) }}\">Compte assigné à l'ensemble des professionnels </li> #} 

        {% else %}
            <li>{{ accountType.limitType }}</li>
            {% if accountType.limitType == 'LIMITED' %}
                <li>{{ accountType.creditLimit }}</li>
            {% endif %}
        {% endif %}
    </div>

        <h3> Types de transfert liés au compte</h3>
            <h4> Transferts vers types de comptes professionnels </h4>
                <table>
                    <tr>
                        <th>Vers le compte</th>
                        <th>Actif</th>
                      {#  <th>Frais de transfert</th> #}
                        <th>Action</th>

                    </tr>
                    {% if accountType.nature == 'USER' %}
                        {% set listTransferTypes = product.userPayments %}
                    {% else %}
                        {% set listTransferTypes = product.systemToUserPayments %}
                    {% endif %}
                    {% for transferType in listTransferTypes %}
                        {% if transferType.from.name == accountType.name %}{# in case of system product, the list contains all system accounts , we need to display the ones related to the provided accountType#}

                            <tr>
                                <td><a href=\"{{path('cairn_user_cyclos_accountsconfig_accounttype_view', {'id': transferType.to.id}) }}\"> {{transferType.to.name}}</a></td>
                                <td> {{transferType.enabled}} </td>
                              {#  <td> {{transferType.transferFee.amount}} % </td> #}
                                <td><a href=\"{{path('cairn_user_cyclos_accountsconfig_transfertype_view', {'id': transferType.id}) }}\">Voir</a></td>
    
                                </a>
                            </tr>
                        {% endif %}
                    {% else %}
                        Pas de type de transfert associé
                    {% endfor %}
                </table>

             <h4> Transferts vers types de comptes système </h4>
                <table>
                    <tr>
                        <th>Vers le compte</th>
                        <th>Actif</th>
                      {#  <th>Frais de transfert</th>#}
                    </tr>
                    {% if accountType.nature == 'USER' %}
                        {% set listTransferTypes = product.systemPayments %}
                    {% else %}
                        {% set listTransferTypes = product.systemToSystemPayments %}
                    {% endif %}

                    {% for transferType in listTransferTypes %}
                        {% if transferType.from.name == accountType.name %}{# in case of system product, the list contains all system accounts , we need to display the ones related to the provided accountType#}
                            <tr>
                                <td><a href=\"{{path('cairn_user_cyclos_accountsconfig_accounttype_view', {'id': transferType.to.id}) }}\"> {{transferType.to.name}}</a></td>
                                <td> {{transferType.enabled}} </td>
                               {# <td>   Pas de frais                    </td>#}
                                <td><a href=\"{{path('cairn_user_cyclos_accountsconfig_transfertype_view', {'id': transferType.id}) }}\">Voir</a></td>
                            </tr>
                        {% endif %}
                    {% endfor %}

                </table>
  </div>

  <p>
    <a href=\"{{ path('cairn_user_cyclos_accountsconfig_accounttype_list') }}\">
      Retour à la liste des comptes
    </a>
     <a href=\"{{ path('cairn_user_cyclos_accountsconfig_accounttype_edit', {'id': accountType.id}) }}\">
      Mettre à jour le type de compte
    </a>
    {% if accountType.nature == 'USER' %}
        {% if isAssigned %}
             <a href=\"{{ path('cairn_user_cyclos_accountsconfig_accounttype_remove_confirm', {'id': accountType.id}) }}\">
              Fermer le type de compte
             </a>

        {% else %}
             <a href=\"{{ path('cairn_user_cyclos_accountsconfig_accounttype_open_confirm', {'id': accountType.id}) }}\">
              Ouvrir le type de compte
            </a>

        {% endif %}
    {% endif %}

  </p>

{% endblock %}
", "CairnUserCyclosBundle:Config/AccountType:view.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/AccountType/view.html.twig");
    }
}

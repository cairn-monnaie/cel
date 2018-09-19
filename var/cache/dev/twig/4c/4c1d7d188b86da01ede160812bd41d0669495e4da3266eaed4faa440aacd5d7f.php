<?php

/* CairnUserBundle:Banking:accounts_list.html.twig */
class __TwigTemplate_48632f4d7a39de2b80772c7bb4f6eee5194ed4828b8de4eaaddf10be14cbaeaa extends Twig_Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:accounts_list.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:accounts_list.html.twig"));

        // line 2
        echo "
<table>
    <tr>
        <td>
            <div>
            <ul>
                ";
        // line 8
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["accounts"]) || array_key_exists("accounts", $context) ? $context["accounts"] : (function () { throw new Twig_Error_Runtime('Variable "accounts" does not exist.', 8, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["account"]) {
            // line 9
            echo "                    ";
            if ((twig_get_attribute($this->env, $this->source, $context["account"], "unlimited", array()) == false)) {
                // line 10
                echo "                        <li class=\"account\" id = \"account_";
                echo twig_escape_filter($this->env, (isset($context["type"]) || array_key_exists("type", $context) ? $context["type"] : (function () { throw new Twig_Error_Runtime('Variable "type" does not exist.', 10, $this->source); })()), "html", null, true);
                echo "\"> <em class=\"account_owner\"> 
                             ";
                // line 11
                if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "type", array()), "nature", array()) == "SYSTEM")) {
                    // line 12
                    echo "                                 SYSTEM
                             ";
                } else {
                    // line 14
                    echo "                                 ";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "owner", array()), "display", array()), "html", null, true);
                    echo " 
                             ";
                }
                // line 16
                echo "                        </em> </br> 
                        <span class=\"account_type\">";
                // line 17
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "type", array()), "name", array()), "html", null, true);
                echo " </span> </br>
                        <span class=\"account_balance\">";
                // line 18
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "status", array()), "balance", array()), "html", null, true);
                echo " </span> </br>
                        <span class=\"account_id\">";
                // line 19
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "id", array()), "html", null, true);
                echo " </span></li>
                    ";
            }
            // line 21
            echo "                ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 22
            echo "                    Pas encore de compte à créditer
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['account'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 24
        echo "            </ul>
            </div>
        </td>
    </tr>
</table>

";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:accounts_list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  90 => 24,  83 => 22,  78 => 21,  73 => 19,  69 => 18,  65 => 17,  62 => 16,  56 => 14,  52 => 12,  50 => 11,  45 => 10,  42 => 9,  37 => 8,  29 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/accounts_list.html.twig #}

<table>
    <tr>
        <td>
            <div>
            <ul>
                {% for account in accounts %}
                    {% if account.unlimited == false %}
                        <li class=\"account\" id = \"account_{{type}}\"> <em class=\"account_owner\"> 
                             {% if account.type.nature == 'SYSTEM' %}
                                 SYSTEM
                             {% else %}
                                 {{account.owner.display}} 
                             {% endif %}
                        </em> </br> 
                        <span class=\"account_type\">{{account.type.name}} </span> </br>
                        <span class=\"account_balance\">{{account.status.balance}} </span> </br>
                        <span class=\"account_id\">{{account.id}} </span></li>
                    {% endif %}
                {% else %}
                    Pas encore de compte à créditer
                {% endfor %}
            </ul>
            </div>
        </td>
    </tr>
</table>

", "CairnUserBundle:Banking:accounts_list.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/accounts_list.html.twig");
    }
}

<?php

/* CairnUserBundle:Banking:transfer_view.html.twig */
class __TwigTemplate_d4b6fa5830f7a274ad16b69239915f027e0321415df2710315bb4dcdf94288b1 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:transfer_view.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:transfer_view.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:transfer_view.html.twig"));

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

         <a href=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transfer_notice_download", array("id" => twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 9, $this->source); })()), "id", array()))), "html", null, true);
        echo "\"> Avis d'opération </a>
        <h1> Détail du virement </h1>

        <h2> Compte à débiter </h2>
            <ul>
                <li> Nom : ";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 14, $this->source); })()), "from", array()), "type", array()), "name", array()), "html", null, true);
        echo "</li>
                <li> ICC : ";
        // line 15
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 15, $this->source); })()), "from", array()), "id", array()), "html", null, true);
        echo "</li>
                <li> Appartient à : 
                    ";
        // line 17
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "from", array(), "any", false, true), "owner", array(), "any", false, true), "display", array(), "any", true, true)) {
            // line 18
            echo "                        ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 18, $this->source); })()), "from", array()), "owner", array()), "display", array()), "html", null, true);
            echo "
                    ";
        } else {
            // line 20
            echo "                        Association Le Cairn
                    ";
        }
        // line 22
        echo "                </li>
            </ul>

        <h2> Compte à créditer </h2>
            <ul>
                <li>Nom : ";
        // line 27
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 27, $this->source); })()), "to", array()), "type", array()), "name", array()), "html", null, true);
        echo "</li>
                <li> ICC : ";
        // line 28
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 28, $this->source); })()), "to", array()), "id", array()), "html", null, true);
        echo "</li>
                <li> Appartient à : 
                    ";
        // line 30
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transfer"] ?? null), "to", array(), "any", false, true), "owner", array(), "any", false, true), "display", array(), "any", true, true)) {
            // line 31
            echo "                        ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 31, $this->source); })()), "to", array()), "owner", array()), "display", array()), "html", null, true);
            echo "
                    ";
        } else {
            // line 33
            echo "                        Association Le Cairn
                    ";
        }
        // line 35
        echo "                </li>

            </ul>

        <em> Date : ";
        // line 39
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 39, $this->source); })()), "date", array()), "html", null, true);
        echo " </em> </br>
        <em> Montant : ";
        // line 40
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 40, $this->source); })()), "currencyAmount", array()), "amount", array()), "html", null, true);
        echo " </em></br>
        <em> Devise : ";
        // line 41
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["transfer"]) || array_key_exists("transfer", $context) ? $context["transfer"] : (function () { throw new Twig_Error_Runtime('Variable "transfer" does not exist.', 41, $this->source); })()), "currencyAmount", array()), "currency", array()), "name", array()), "html", null, true);
        echo " </em></br>

      ";
        // line 44
        echo "    </div>    
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:transfer_view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  136 => 44,  131 => 41,  127 => 40,  123 => 39,  117 => 35,  113 => 33,  107 => 31,  105 => 30,  100 => 28,  96 => 27,  89 => 22,  85 => 20,  79 => 18,  77 => 17,  72 => 15,  68 => 14,  60 => 9,  53 => 6,  44 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/transfer_view.html.twig #}

{% extends \"CairnUserBundle::layout.html.twig\" %}

{% block body %}
    {{parent()}}
    <div>

         <a href=\"{{path('cairn_user_banking_transfer_notice_download',{'id':transfer.id})}}\"> Avis d'opération </a>
        <h1> Détail du virement </h1>

        <h2> Compte à débiter </h2>
            <ul>
                <li> Nom : {{transfer.from.type.name}}</li>
                <li> ICC : {{transfer.from.id}}</li>
                <li> Appartient à : 
                    {% if transfer.from.owner.display is defined %}
                        {{transfer.from.owner.display}}
                    {% else %}
                        Association Le Cairn
                    {% endif %}
                </li>
            </ul>

        <h2> Compte à créditer </h2>
            <ul>
                <li>Nom : {{transfer.to.type.name}}</li>
                <li> ICC : {{transfer.to.id}}</li>
                <li> Appartient à : 
                    {% if transfer.to.owner.display is defined %}
                        {{transfer.to.owner.display}}
                    {% else %}
                        Association Le Cairn
                    {% endif %}
                </li>

            </ul>

        <em> Date : {{transfer.date}} </em> </br>
        <em> Montant : {{transfer.currencyAmount.amount}} </em></br>
        <em> Devise : {{transfer.currencyAmount.currency.name}} </em></br>

      {# <h3> Description : {{transfer.description}} </h3>#}
    </div>    
{% endblock %}
", "CairnUserBundle:Banking:transfer_view.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/transfer_view.html.twig");
    }
}

<?php

/* CairnUserBundle:Pdf:rib_cairn.html.twig */
class __TwigTemplate_f83b1027b265b62811c3419be998a5e0d30effe2d0dadaba6b84cfbe26d38e3a extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout-pdf.html.twig", "CairnUserBundle:Pdf:rib_cairn.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Pdf:rib_cairn.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Pdf:rib_cairn.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 5
    public function block_fos_user_content($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "fos_user_content"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "fos_user_content"));

        // line 6
        echo "
    <table>
        <thead>
            <tr>
                <th> Nom du compte </th>
                <th> Identifiant </th>
                <th> Devise </th> 
            </tr>
        </thead>
        <tbody>
             <tr>
                <td> ";
        // line 17
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["account"]) || array_key_exists("account", $context) ? $context["account"] : (function () { throw new Twig_Error_Runtime('Variable "account" does not exist.', 17, $this->source); })()), "type", array()), "name", array()), "html", null, true);
        echo " </td>
                <td> ";
        // line 18
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["account"]) || array_key_exists("account", $context) ? $context["account"] : (function () { throw new Twig_Error_Runtime('Variable "account" does not exist.', 18, $this->source); })()), "id", array()), "html", null, true);
        echo " </td>
                <td> ";
        // line 19
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["account"]) || array_key_exists("account", $context) ? $context["account"] : (function () { throw new Twig_Error_Runtime('Variable "account" does not exist.', 19, $this->source); })()), "currency", array()), "suffix", array()), "html", null, true);
        echo " </td>
            </tr>
            <tr> 
                ";
        // line 22
        if ((twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["account"]) || array_key_exists("account", $context) ? $context["account"] : (function () { throw new Twig_Error_Runtime('Variable "account" does not exist.', 22, $this->source); })()), "type", array()), "nature", array()) == "SYSTEM")) {
            // line 23
            echo "                    ";
            $context["name"] = "Cairn, Monnaie Locale Complémentaire";
            // line 24
            echo "                ";
        } else {
            // line 25
            echo "                    ";
            $context["name"] = twig_get_attribute($this->env, $this->source, (isset($context["owner"]) || array_key_exists("owner", $context) ? $context["owner"] : (function () { throw new Twig_Error_Runtime('Variable "owner" does not exist.', 25, $this->source); })()), "name", array());
            // line 26
            echo "                ";
        }
        // line 27
        echo "                    <div> <strong> Titulaire du compte </strong> </div>
                    <div> ";
        // line 28
        echo twig_escape_filter($this->env, (isset($context["name"]) || array_key_exists("name", $context) ? $context["name"] : (function () { throw new Twig_Error_Runtime('Variable "name" does not exist.', 28, $this->source); })()), "html", null, true);
        echo " </div>
                    <div> ";
        // line 29
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["owner"]) || array_key_exists("owner", $context) ? $context["owner"] : (function () { throw new Twig_Error_Runtime('Variable "owner" does not exist.', 29, $this->source); })()), "address", array()), "street", array()), "html", null, true);
        echo " </div>
                    <div> ";
        // line 30
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["owner"]) || array_key_exists("owner", $context) ? $context["owner"] : (function () { throw new Twig_Error_Runtime('Variable "owner" does not exist.', 30, $this->source); })()), "city", array()), "html", null, true);
        echo " </div>
            </tr>
        </tbody>
    </table>

";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Pdf:rib_cairn.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  105 => 30,  101 => 29,  97 => 28,  94 => 27,  91 => 26,  88 => 25,  85 => 24,  82 => 23,  80 => 22,  74 => 19,  70 => 18,  66 => 17,  53 => 6,  44 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Pdf/rib_cairn.html.twig #}         

{% extends 'CairnUserBundle::layout-pdf.html.twig' %}

{% block fos_user_content %}

    <table>
        <thead>
            <tr>
                <th> Nom du compte </th>
                <th> Identifiant </th>
                <th> Devise </th> 
            </tr>
        </thead>
        <tbody>
             <tr>
                <td> {{account.type.name}} </td>
                <td> {{account.id}} </td>
                <td> {{account.currency.suffix}} </td>
            </tr>
            <tr> 
                {% if account.type.nature == 'SYSTEM' %}
                    {% set name = 'Cairn, Monnaie Locale Complémentaire' %}
                {% else %}
                    {% set name = owner.name %}
                {% endif %}
                    <div> <strong> Titulaire du compte </strong> </div>
                    <div> {{name}} </div>
                    <div> {{ owner.address.street }} </div>
                    <div> {{ owner.city }} </div>
            </tr>
        </tbody>
    </table>

{% endblock %}
", "CairnUserBundle:Pdf:rib_cairn.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pdf/rib_cairn.html.twig");
    }
}

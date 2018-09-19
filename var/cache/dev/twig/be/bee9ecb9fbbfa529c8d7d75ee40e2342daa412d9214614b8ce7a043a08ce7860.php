<?php

/* CairnUserBundle:Banking:conversion_operations.html.twig */
class __TwigTemplate_7fbedbebd636cfaf67b137a97ae8650a5c01d3e1db8f403477ba4595e8356be0 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:conversion_operations.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:conversion_operations.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:conversion_operations.html.twig"));

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

    <div class=\"body_wrapper>
        <div id=\"conversion_action\">
            <h1> Effectuer une conversion </h1>
           <ul>
                ";
        // line 12
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
            // line 13
            echo "                    <li><a href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_conversion_request", array("to" => "self"));
            echo "\"> Vers un compte de l'Association</a></li>
                    <li><a href=\"";
            // line 14
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_conversion_request", array("to" => "other"));
            echo "\"> Vers un professionnel </a></li> 
                ";
        } else {
            // line 16
            echo "                     <li><a href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_conversion_request", array("to" => "self"));
            echo "\"> Effectuer une conversion</a></li>
                ";
        }
        // line 18
        echo "
           <ul> 
        </div>
        <div id=\"conversion_management\">
            <h1> Gérer les conversions </h1>
            <ul>
                <li><a href=\"";
        // line 24
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations_view", array("frequency" => "unique", "type" => "conversion")), "html", null, true);
        echo "\"> Voir mes conversions </a></li>
            </ul>
        </div>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:conversion_operations.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  89 => 24,  81 => 18,  75 => 16,  70 => 14,  65 => 13,  63 => 12,  53 => 6,  44 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/conversion_operations.html.twig #}

{% extends \"CairnUserBundle::layout.html.twig\" %}

{% block body %}
    {{parent()}}

    <div class=\"body_wrapper>
        <div id=\"conversion_action\">
            <h1> Effectuer une conversion </h1>
           <ul>
                {% if is_granted('ROLE_SUPER_ADMIN') %}
                    <li><a href=\"{{path('cairn_user_banking_conversion_request',{'to': 'self'})}}\"> Vers un compte de l'Association</a></li>
                    <li><a href=\"{{path('cairn_user_banking_conversion_request',{'to': 'other'})}}\"> Vers un professionnel </a></li> 
                {% else %}
                     <li><a href=\"{{path('cairn_user_banking_conversion_request',{'to': 'self'})}}\"> Effectuer une conversion</a></li>
                {% endif %}

           <ul> 
        </div>
        <div id=\"conversion_management\">
            <h1> Gérer les conversions </h1>
            <ul>
                <li><a href=\"{{path('cairn_user_banking_operations_view',{'frequency':'unique','type':'conversion'})}}\"> Voir mes conversions </a></li>
            </ul>
        </div>
{% endblock %}


", "CairnUserBundle:Banking:conversion_operations.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/conversion_operations.html.twig");
    }
}

<?php

/* CairnUserBundle:Banking:deposit_operations.html.twig */
class __TwigTemplate_79d6b655e554aa31ede041c16f44819eef135f64267f3d399db001713fa18eca extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:deposit_operations.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:deposit_operations.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:deposit_operations.html.twig"));

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
        <div id=\"deposit_action\">
            <h1> Les dépôts </h1>
           <ul>
                <li><a href=\"";
        // line 12
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_deposit_request");
        echo "\"> Effectuer un dépôt     </a> </li>
               ";
        // line 14
        echo "
           <ul> 
        </div>
        <div id=\"deposit_management\">
            <h1> Gérer les dépôts </h1>
            <ul>
                <li><a href=\"";
        // line 20
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations_view", array("frequency" => "unique", "type" => "deposit")), "html", null, true);
        echo "\"> Voir les dépôts </a></li>
                <li><a href=\"";
        // line 21
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_beneficiaries_list");
        echo "\"> Mes favoris </a></li>
            </ul>
        </div>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:deposit_operations.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  79 => 21,  75 => 20,  67 => 14,  63 => 12,  53 => 6,  44 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/deposit_operations.html.twig #}

{% extends \"CairnUserBundle::layout.html.twig\" %}

{% block body %}
    {{parent()}}

    <div class=\"body_wrapper>
        <div id=\"deposit_action\">
            <h1> Les dépôts </h1>
           <ul>
                <li><a href=\"{{path('cairn_user_banking_deposit_request')}}\"> Effectuer un dépôt     </a> </li>
               {# <li><a href=\"{{path('cairn_user_banking_deposit_request',{'frequency': 'recurring'})}}\"> Parmi les favoris </a></li>#}

           <ul> 
        </div>
        <div id=\"deposit_management\">
            <h1> Gérer les dépôts </h1>
            <ul>
                <li><a href=\"{{path('cairn_user_banking_operations_view',{'frequency':'unique','type':'deposit'})}}\"> Voir les dépôts </a></li>
                <li><a href=\"{{path('cairn_user_beneficiaries_list')}}\"> Mes favoris </a></li>
            </ul>
        </div>
{% endblock %}


", "CairnUserBundle:Banking:deposit_operations.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/deposit_operations.html.twig");
    }
}

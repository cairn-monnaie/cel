<?php

/* CairnUserBundle:Banking:transaction_to.html.twig */
class __TwigTemplate_1017ee23aa98fcdf60f85ed7017620b41224be4a7994cff8b7462c58f0d39fb9 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:transaction_to.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:transaction_to.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:transaction_to.html.twig"));

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
        <div id=\"transaction_to\">
           <ul>
                <li><a href=\"";
        // line 11
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_request", array("frequency" => (isset($context["frequency"]) || array_key_exists("frequency", $context) ? $context["frequency"] : (function () { throw new Twig_Error_Runtime('Variable "frequency" does not exist.', 11, $this->source); })()), "to" => "self")), "html", null, true);
        echo "\"> Entre vos comptes     </a></li>
                <li><a href=\"";
        // line 12
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_request", array("frequency" => (isset($context["frequency"]) || array_key_exists("frequency", $context) ? $context["frequency"] : (function () { throw new Twig_Error_Runtime('Variable "frequency" does not exist.', 12, $this->source); })()), "to" => "beneficiary")), "html", null, true);
        echo "\"> Vers un bénéficiaire enregistré </a></li>

                <li><a href=\"";
        // line 14
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_request", array("frequency" => (isset($context["frequency"]) || array_key_exists("frequency", $context) ? $context["frequency"] : (function () { throw new Twig_Error_Runtime('Variable "frequency" does not exist.', 14, $this->source); })()), "to" => "new")), "html", null, true);
        echo "\"> Vers un nouveau bénéficiaire </a></li>
           <ul> 
        </div>
            </ul>
        </div>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:transaction_to.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 14,  66 => 12,  62 => 11,  53 => 6,  44 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/transaction_to.html.twig #}

{% extends \"CairnUserBundle::layout.html.twig\" %}

{% block body %}
    {{parent()}}

    <div class=\"body_wrapper>
        <div id=\"transaction_to\">
           <ul>
                <li><a href=\"{{path('cairn_user_banking_transaction_request',{'frequency': frequency, 'to':'self'})}}\"> Entre vos comptes     </a></li>
                <li><a href=\"{{path('cairn_user_banking_transaction_request',{'frequency':frequency,'to':'beneficiary'})}}\"> Vers un bénéficiaire enregistré </a></li>

                <li><a href=\"{{path('cairn_user_banking_transaction_request',{'frequency':frequency,'to':'new'})}}\"> Vers un nouveau bénéficiaire </a></li>
           <ul> 
        </div>
            </ul>
        </div>
{% endblock %}


", "CairnUserBundle:Banking:transaction_to.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/transaction_to.html.twig");
    }
}

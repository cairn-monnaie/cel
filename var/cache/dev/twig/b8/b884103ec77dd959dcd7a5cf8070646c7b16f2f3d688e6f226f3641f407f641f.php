<?php

/* CairnUserCyclosBundle:Config/Network:view.html.twig */
class __TwigTemplate_be629c457277dafe6ac8ef68ce5c6f0777b76d83b6423b68220f03c0bab37e15 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/Network:view.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserCyclosBundle:Config/Network:view.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserCyclosBundle:Config/Network:view.html.twig"));

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
    ";
        // line 8
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
            // line 9
            echo "  <h2>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["network"]) || array_key_exists("network", $context) ? $context["network"] : (function () { throw new Twig_Error_Runtime('Variable "network" does not exist.', 9, $this->source); })()), "name", array()), "html", null, true);
            echo "</h2>

  <div class=\"well\">
    ";
            // line 12
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["network"]) || array_key_exists("network", $context) ? $context["network"] : (function () { throw new Twig_Error_Runtime('Variable "network" does not exist.', 12, $this->source); })()), "name", array()), "html", null, true);
            echo "
  </div>
    ";
        }
        // line 15
        echo "  <p>
    ";
        // line 16
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
            // line 17
            echo "       ";
            // line 20
            echo "
        <a href=\"";
            // line 21
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_home");
            echo "\" >
        Gestion des comptes
        </a>

    ";
        }
        // line 26
        echo "  </p>

";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/Network:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  91 => 26,  83 => 21,  80 => 20,  78 => 17,  76 => 16,  73 => 15,  67 => 12,  60 => 9,  58 => 8,  53 => 7,  44 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserCyclosBundle/Resources/view/Network/view.html.twig #}

{% extends \"CairnUserCyclosBundle::layout.html.twig\" %}


{% block body %}
    {{ parent() }}
    {% if is_granted('ROLE_SUPER_ADMIN') %}
  <h2>{{ network.name }}</h2>

  <div class=\"well\">
    {{ network.name }}
  </div>
    {% endif %}
  <p>
    {% if is_granted('ROLE_SUPER_ADMIN') %}
       {# <a href=\"{{ path('cairn_user_cyclos_sysconfig_home', {'id': network.id}) }}\" >
        Gestion du système
        </a>#}

        <a href=\"{{ path('cairn_user_cyclos_accountsconfig_accounttype_home') }}\" >
        Gestion des comptes
        </a>

    {% endif %}
  </p>

{% endblock %}
", "CairnUserCyclosBundle:Config/Network:view.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/Network/view.html.twig");
    }
}

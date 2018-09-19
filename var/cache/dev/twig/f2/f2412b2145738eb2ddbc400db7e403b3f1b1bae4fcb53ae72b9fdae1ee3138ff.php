<?php

/* CairnUserCyclosBundle:Config/AccountType:list.html.twig */
class __TwigTemplate_47735c55a71807be013c8f84c350685faa794c783b3de174570a698aa5cd6e30 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/AccountType:list.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserCyclosBundle:Config/AccountType:list.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserCyclosBundle:Config/AccountType:list.html.twig"));

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
  <h2>Liste des types de compte</h2>

  <ul>
    <h3> Comptes Système
    ";
        // line 12
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["systemAccountTypes"]) || array_key_exists("systemAccountTypes", $context) ? $context["systemAccountTypes"] : (function () { throw new Twig_Error_Runtime('Variable "systemAccountTypes" does not exist.', 12, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["accounttype"]) {
            // line 13
            echo "      <li>
        <a href=\"";
            // line 14
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_view", array("id" => twig_get_attribute($this->env, $this->source, $context["accounttype"], "id", array()))), "html", null, true);
            echo "\">
          ";
            // line 15
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["accounttype"], "name", array()), "html", null, true);
            echo "
        </a>
      </li>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 19
            echo "      <li>Pas (encore !) de compte système </li>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['accounttype'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 21
        echo "  </ul>

   ";
        // line 27
        echo "
  <ul>

    <h3> Comptes Professionnels
    ";
        // line 31
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["userAccountTypes"]) || array_key_exists("userAccountTypes", $context) ? $context["userAccountTypes"] : (function () { throw new Twig_Error_Runtime('Variable "userAccountTypes" does not exist.', 31, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["accounttype"]) {
            // line 32
            echo "      <li>
        <a href=\"";
            // line 33
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_view", array("id" => twig_get_attribute($this->env, $this->source, $context["accounttype"], "id", array()))), "html", null, true);
            echo "\">
          ";
            // line 34
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["accounttype"], "name", array()), "html", null, true);
            echo "
        </a>
      </li>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 38
            echo "      <li>Pas (encore !) de compte professionnel </li>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['accounttype'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 40
        echo "  </ul>
    <a href=\"";
        // line 41
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_add", array("nature" => "USER"));
        echo "\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-edit\"></i>
      Ajouter un type de compte Pro
    </a>

  <p>
   ";
        // line 51
        echo "


  </p>

";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/AccountType:list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  140 => 51,  131 => 41,  128 => 40,  121 => 38,  112 => 34,  108 => 33,  105 => 32,  100 => 31,  94 => 27,  90 => 21,  83 => 19,  74 => 15,  70 => 14,  67 => 13,  62 => 12,  53 => 7,  44 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/AccountTypeCyclosBundle/Resources/views/Config/AccountType/list.html.twig #}

{% extends \"CairnUserCyclosBundle::layout.html.twig\" %}


{% block body %}
    {{parent()}}
  <h2>Liste des types de compte</h2>

  <ul>
    <h3> Comptes Système
    {% for accounttype in systemAccountTypes %}
      <li>
        <a href=\"{{ path('cairn_user_cyclos_accountsconfig_accounttype_view', {'id': accounttype.id}) }}\">
          {{ accounttype.name }}
        </a>
      </li>
    {% else %}
      <li>Pas (encore !) de compte système </li>
    {% endfor %}
  </ul>

   {# <a href=\"{{ path('cairn_user_cyclos_accountsconfig_accounttype_add', {'nature' : 'SYSTEM'}) }}\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-edit\"></i>
      Ajouter un type de compte système
    </a> #}

  <ul>

    <h3> Comptes Professionnels
    {% for accounttype in userAccountTypes %}
      <li>
        <a href=\"{{ path('cairn_user_cyclos_accountsconfig_accounttype_view', {'id': accounttype.id}) }}\">
          {{ accounttype.name }}
        </a>
      </li>
    {% else %}
      <li>Pas (encore !) de compte professionnel </li>
    {% endfor %}
  </ul>
    <a href=\"{{ path('cairn_user_cyclos_accountsconfig_accounttype_add', {'nature' : 'USER'}) }}\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-edit\"></i>
      Ajouter un type de compte Pro
    </a>

  <p>
   {# <a href=\"{{ path('cairn_user_cyclos_accountsconfig_accounttype_home') }}\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-left\"></i>
      Retourner à la liste des devises
    </a>#}



  </p>

{% endblock %}
", "CairnUserCyclosBundle:Config/AccountType:list.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/AccountType/list.html.twig");
    }
}

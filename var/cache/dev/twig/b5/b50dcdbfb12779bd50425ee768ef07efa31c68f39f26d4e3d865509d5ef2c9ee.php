<?php

/* CairnUserBundle::layout.html.twig */
class __TwigTemplate_98f396342a0f8a9204484cb4100cc05fa393cd2a6b6ed3e9f8e1e28ec15abf6c extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("::layout.html.twig", "CairnUserBundle::layout.html.twig", 3);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle::layout.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle::layout.html.twig"));

        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "title"));

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    // line 7
    public function block_body($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 8
        echo "
<nav>
    ";
        // line 10
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_ADMIN")) {
            // line 11
            echo "
    <ul>
        <li id=\"id_welcome\" class=\"menu_depliant\">
            <a class=\"menu-item\" href=\"";
            // line 14
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_welcome");
            echo "\">Accueil</a>
        </li>
        <li>    
            <a class=\"menu-item\" href=\"";
            // line 17
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_accounts_overview", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 17, $this->source); })()), "user", array()), "id", array()))), "html", null, true);
            echo "\"> Situation </a>
        </li>

       ";
            // line 23
            echo "        <li>    
            <a class=\"menu-item\" href=\"";
            // line 24
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_home");
            echo "\">Les membres</a>
        </li>

        <li>
            <a class=\"menu-item\" href=\"#\">Opérations</a>
             <div class=\"menu-content\">
                <ul class=\"menu_list\">
                    ";
            // line 31
            if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
                // line 32
                echo "                        <li><a class=\"menu-subitem\" href=\"";
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "transaction"));
                echo "\"> Virements </a></li>
                    ";
            }
            // line 34
            echo "                    <li><a class=\"menu-subitem\" href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "conversion"));
            echo "\"> Conversion </a></li>
                    <li><a class=\"menu-subitem\" href=\"";
            // line 35
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "deposit"));
            echo "\"> Dépôt </a></li>
                    <li><a class=\"menu-subitem\" href=\"";
            // line 36
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "withdrawal"));
            echo "\"> Retrait </a></li>

                </ul>
            </div>

        </li>
        <li><a class=\"menu-item\" href=\"";
            // line 42
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_profile_view", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 42, $this->source); })()), "user", array()), "id", array()))), "html", null, true);
            echo "\"> Profil </a></li>
        

        ";
            // line 45
            if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
                // line 46
                echo "            <li><a class=\"menu-item\" href=\"";
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_config_home");
                echo "\">Espace configuration</a></li>
        ";
            }
            // line 48
            echo "    </ul>


    ";
        } elseif ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_PRO")) {
            // line 52
            echo "
    <ul>
        <li id=\"id_welcome\" class=\"menu_depliant\">
            <a class=\"menu-item\" href=\"";
            // line 55
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_welcome");
            echo "\"> Accueil </a>
        </li>
        <li>    
            <a class=\"menu-item\" href=\"";
            // line 58
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_accounts_overview", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 58, $this->source); })()), "user", array()), "id", array()))), "html", null, true);
            echo "\"> Situation </a>

        </li>
        <li>
            <a class=\"menu-item\" href=\"#\"> Opérations </a>
             <div class=\"menu-content\">
                <ul class=\"menu_list\">
                    <li><a class=\"menu-subitem\" href=\"";
            // line 65
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "transaction"));
            echo "\"> Virements </a></li>
                    <li><a class=\"menu-subitem\" href=\"";
            // line 66
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "conversion"));
            echo "\"> Conversion </a></li>
                    <li><a class=\"menu-subitem\" href=\"";
            // line 67
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations", array("type" => "reconversion"));
            echo "\"> Reconversion </a></li>
                </ul>
            </div>
           
        </li>
        <li><a class=\"menu-item\" href=\"";
            // line 72
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_profile_view", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 72, $this->source); })()), "user", array()), "id", array()))), "html", null, true);
            echo "\"> Profil </a></li>
        <li><a class=\"menu-item\" href=\"#\"> Etendre son réseau </a></li>
    </ul>
    ";
        }
        // line 76
        echo "</nav>
<div id=\"flash_messages\">
   ";
        // line 78
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 78, $this->source); })()), "session", array()), "flashbag", array()), "all", array(), "method"));
        foreach ($context['_seq'] as $context["key"] => $context["messages"]) {
            echo "                      
          ";
            // line 79
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($context["messages"]);
            foreach ($context['_seq'] as $context["_key"] => $context["message"]) {
                echo "                                          
              <div class=\"alert alert-";
                // line 80
                echo twig_escape_filter($this->env, $context["key"], "html", null, true);
                echo "\">                                
              ";
                // line 81
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans($context["message"], array(), "FOSUserBundle"), "html", null, true);
                echo "                           
              </div>                                                             
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['message'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 83
            echo "                                                           
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['messages'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 84
        echo "  
<div>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    // line 89
    public function block_javascripts($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "javascripts"));

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle::layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  246 => 89,  234 => 84,  227 => 83,  218 => 81,  214 => 80,  208 => 79,  202 => 78,  198 => 76,  191 => 72,  183 => 67,  179 => 66,  175 => 65,  165 => 58,  159 => 55,  154 => 52,  148 => 48,  142 => 46,  140 => 45,  134 => 42,  125 => 36,  121 => 35,  116 => 34,  110 => 32,  108 => 31,  98 => 24,  95 => 23,  89 => 17,  83 => 14,  78 => 11,  76 => 10,  72 => 8,  63 => 7,  46 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/layout.html.twig #}         
                                                                               
{% extends \"::layout.html.twig\" %}                        
                                                                               
{% block title %}{% endblock %}                                                                 
                                                                               
{% block body %}

<nav>
    {% if is_granted(\"ROLE_ADMIN\") %}

    <ul>
        <li id=\"id_welcome\" class=\"menu_depliant\">
            <a class=\"menu-item\" href=\"{{path('cairn_user_welcome')}}\">Accueil</a>
        </li>
        <li>    
            <a class=\"menu-item\" href=\"{{path('cairn_user_banking_accounts_overview',{'id':app.user.id})}}\"> Situation </a>
        </li>

       {# <li>    
            <a class=\"menu-item\" href=\"{{path('cairn_user_banknotes_home')}}\">Les billets</a>
        </li>#}
        <li>    
            <a class=\"menu-item\" href=\"{{path('cairn_user_users_home')}}\">Les membres</a>
        </li>

        <li>
            <a class=\"menu-item\" href=\"#\">Opérations</a>
             <div class=\"menu-content\">
                <ul class=\"menu_list\">
                    {% if is_granted(\"ROLE_SUPER_ADMIN\") %}
                        <li><a class=\"menu-subitem\" href=\"{{path('cairn_user_banking_operations',{'type':'transaction'})}}\"> Virements </a></li>
                    {% endif %}
                    <li><a class=\"menu-subitem\" href=\"{{path('cairn_user_banking_operations',{'type':'conversion'})}}\"> Conversion </a></li>
                    <li><a class=\"menu-subitem\" href=\"{{path('cairn_user_banking_operations',{'type':'deposit'})}}\"> Dépôt </a></li>
                    <li><a class=\"menu-subitem\" href=\"{{path('cairn_user_banking_operations',{'type':'withdrawal'})}}\"> Retrait </a></li>

                </ul>
            </div>

        </li>
        <li><a class=\"menu-item\" href=\"{{path('cairn_user_profile_view',{'id' : app.user.id})}}\"> Profil </a></li>
        

        {% if is_granted('ROLE_SUPER_ADMIN') %}
            <li><a class=\"menu-item\" href=\"{{path('cairn_user_cyclos_config_home')}}\">Espace configuration</a></li>
        {% endif %}
    </ul>


    {% elseif is_granted(\"ROLE_PRO\") %}

    <ul>
        <li id=\"id_welcome\" class=\"menu_depliant\">
            <a class=\"menu-item\" href=\"{{path('cairn_user_welcome')}}\"> Accueil </a>
        </li>
        <li>    
            <a class=\"menu-item\" href=\"{{path('cairn_user_banking_accounts_overview',{'id':app.user.id})}}\"> Situation </a>

        </li>
        <li>
            <a class=\"menu-item\" href=\"#\"> Opérations </a>
             <div class=\"menu-content\">
                <ul class=\"menu_list\">
                    <li><a class=\"menu-subitem\" href=\"{{path('cairn_user_banking_operations',{'type':'transaction'})}}\"> Virements </a></li>
                    <li><a class=\"menu-subitem\" href=\"{{path('cairn_user_banking_operations',{'type':'conversion'})}}\"> Conversion </a></li>
                    <li><a class=\"menu-subitem\" href=\"{{path('cairn_user_banking_operations',{'type':'reconversion'})}}\"> Reconversion </a></li>
                </ul>
            </div>
           
        </li>
        <li><a class=\"menu-item\" href=\"{{path('cairn_user_profile_view',{'id' : app.user.id})}}\"> Profil </a></li>
        <li><a class=\"menu-item\" href=\"#\"> Etendre son réseau </a></li>
    </ul>
    {% endif %}
</nav>
<div id=\"flash_messages\">
   {% for key, messages in app.session.flashbag.all() %}                      
          {% for message in messages %}                                          
              <div class=\"alert alert-{{ key }}\">                                
              {{ message|trans({}, 'FOSUserBundle') }}                           
              </div>                                                             
          {% endfor %}                                                           
    {% endfor %}  
<div>
{% endblock %}
                                                                               

{% block javascripts %}
{% endblock %}

", "CairnUserBundle::layout.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/layout.html.twig");
    }
}

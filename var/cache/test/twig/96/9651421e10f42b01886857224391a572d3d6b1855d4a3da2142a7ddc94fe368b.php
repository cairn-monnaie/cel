<?php

/* CairnUserBundle:User:list_users.html.twig */
class __TwigTemplate_c12fe80727de500a3c67f7daf0f2f63a84efafa8cbb29308d00be31e62194c20 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:User:list_users.html.twig", 3);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:User:list_users.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:User:list_users.html.twig"));

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
    public function block_stylesheets($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "stylesheets"));

        // line 8
        echo "    <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("layout-style.css"), "html", null, true);
        echo "\" type=\"text/css\" /> 
    <link rel=\"stylesheet\" href=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("pro.css"), "html", null, true);
        echo "\" type=\"text/css\" /> 

";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    // line 12
    public function block_body($context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "block", "body"));

        // line 13
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo " 
<div>                                                                          
    ";
        // line 15
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_ADMIN")) {
            // line 16
            echo "        <ul>
        ";
            // line 17
            if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
                // line 18
                echo "            <li><a href=\"";
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_block_all");
                echo "\" >Fermer l'accès à la plateforme</a></li>
            <li><a href=\"";
                // line 19
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_activate_all");
                echo "\" >Ouvrir l'accès à la plateforme</a></li>
        ";
            }
            // line 21
            echo "            <li><a href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration");
            echo "\" >Ajouter un membre</a></li> 
            <li><a href=\"";
            // line 22
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_search");
            echo "\" >Rechercher un professionnel</a></li>
        </ul>
    ";
        }
        // line 25
        echo "    <div>
        <h2> Les professionnels </h2>
        <ul>
            ";
        // line 28
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["listPros"]) || array_key_exists("listPros", $context) ? $context["listPros"] : (function () { throw new Twig_Error_Runtime('Variable "listPros" does not exist.', 28, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["user"]) {
            // line 29
            echo "               <li><a href=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_profile_view", array("id" => twig_get_attribute($this->env, $this->source, $context["user"], "id", array()))), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "name", array()), "html", null, true);
            echo "</a></li>
            ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 30
            echo "  
                Pas <em>encore</em> de professionel inscrit !
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['user'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 33
        echo "        </ul>

         <h2> Les groupes locaux </h2>
         <ul>
            ";
        // line 37
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["listAdmins"]) || array_key_exists("listAdmins", $context) ? $context["listAdmins"] : (function () { throw new Twig_Error_Runtime('Variable "listAdmins" does not exist.', 37, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["user"]) {
            // line 38
            echo "               <li><a href=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_profile_view", array("id" => twig_get_attribute($this->env, $this->source, $context["user"], "id", array()))), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "name", array()), "html", null, true);
            echo "</a></li>
            ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 39
            echo "  
                Pas <em>encore</em> de groupe local inscrit !
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['user'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 42
        echo "        </ul>
   
        ";
        // line 44
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
            // line 45
            echo "            <h2> Les administrateurs </h2>
            <ul>
               ";
            // line 47
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["listSuperAdmins"]) || array_key_exists("listSuperAdmins", $context) ? $context["listSuperAdmins"] : (function () { throw new Twig_Error_Runtime('Variable "listSuperAdmins" does not exist.', 47, $this->source); })()));
            $context['_iterated'] = false;
            foreach ($context['_seq'] as $context["_key"] => $context["user"]) {
                // line 48
                echo "                  <li><a href=\"";
                echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_profile_view", array("id" => twig_get_attribute($this->env, $this->source, $context["user"], "id", array()))), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "name", array()), "html", null, true);
                echo "</a></li>
               ";
                $context['_iterated'] = true;
            }
            if (!$context['_iterated']) {
                // line 49
                echo "  
                   Pas <em>encore</em> d'administrateur inscrit !
               ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['user'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 52
            echo "            </ul>
        ";
        }
        // line 54
        echo "    </div>
</div>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:User:list_users.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  227 => 54,  223 => 52,  215 => 49,  205 => 48,  200 => 47,  196 => 45,  194 => 44,  190 => 42,  182 => 39,  172 => 38,  167 => 37,  161 => 33,  153 => 30,  143 => 29,  138 => 28,  133 => 25,  127 => 22,  122 => 21,  117 => 19,  112 => 18,  110 => 17,  107 => 16,  105 => 15,  99 => 13,  90 => 12,  77 => 9,  72 => 8,  63 => 7,  46 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/User/list_users.html.twig #}         

{% extends \"CairnUserBundle::layout.html.twig\" %}                        
                                                                               
{% block title %}{% endblock %}                                                                 
                                                                               
{%block stylesheets %}
    <link rel=\"stylesheet\" href=\"{{ asset('layout-style.css') }}\" type=\"text/css\" /> 
    <link rel=\"stylesheet\" href=\"{{ asset('pro.css') }}\" type=\"text/css\" /> 

{% endblock %}
{% block body %}
    {{parent()}} 
<div>                                                                          
    {% if is_granted('ROLE_ADMIN') %}
        <ul>
        {% if is_granted('ROLE_SUPER_ADMIN') %}
            <li><a href=\"{{path('cairn_user_users_block_all') }}\" >Fermer l'accès à la plateforme</a></li>
            <li><a href=\"{{path('cairn_user_users_activate_all') }}\" >Ouvrir l'accès à la plateforme</a></li>
        {% endif %}
            <li><a href=\"{{path('cairn_user_registration') }}\" >Ajouter un membre</a></li> 
            <li><a href=\"{{path('cairn_user_users_search') }}\" >Rechercher un professionnel</a></li>
        </ul>
    {% endif %}
    <div>
        <h2> Les professionnels </h2>
        <ul>
            {% for user in listPros %}
               <li><a href=\"{{path('cairn_user_profile_view', {'id': user.id})}}\">{{user.name}}</a></li>
            {% else %}  
                Pas <em>encore</em> de professionel inscrit !
            {% endfor %}
        </ul>

         <h2> Les groupes locaux </h2>
         <ul>
            {% for user in listAdmins %}
               <li><a href=\"{{path('cairn_user_profile_view', {'id': user.id})}}\">{{user.name}}</a></li>
            {% else %}  
                Pas <em>encore</em> de groupe local inscrit !
            {% endfor %}
        </ul>
   
        {% if is_granted('ROLE_SUPER_ADMIN') %}
            <h2> Les administrateurs </h2>
            <ul>
               {% for user in listSuperAdmins %}
                  <li><a href=\"{{path('cairn_user_profile_view', {'id': user.id})}}\">{{user.name}}</a></li>
               {% else %}  
                   Pas <em>encore</em> d'administrateur inscrit !
               {% endfor %}
            </ul>
        {% endif %}
    </div>
</div>
{% endblock %}              

", "CairnUserBundle:User:list_users.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/User/list_users.html.twig");
    }
}

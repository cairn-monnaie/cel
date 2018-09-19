<?php

/* CairnUserBundle:Pro:view.html.twig */
class __TwigTemplate_19aa7902a2784fc73da85471e6572e7806df02b36a01f930a8d7becab61d7fc2 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Pro:view.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Pro:view.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Pro:view.html.twig"));

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
    
    ";
        // line 15
        $context["card"] = twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 15, $this->source); })()), "card", array());
        // line 16
        echo "    <div class=\"profile_wrapper\">
        <ul>
            <li>";
        // line 18
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 18, $this->source); })()), "name", array()), "html", null, true);
        echo "</li>                
            <li>";
        // line 19
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 19, $this->source); })()), "username", array()), "html", null, true);
        echo " </li>
            <li>";
        // line 20
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 20, $this->source); })()), "email", array()), "html", null, true);
        echo "</li>
            <li>
                <div id=\"user_address\">                
                    ";
        // line 23
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 23, $this->source); })()), "address", array()), "street", array()), "html", null, true);
        echo " </br>
                    ";
        // line 24
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 24, $this->source); })()), "address", array()), "zipCity", array()), "zipCode", array()), "html", null, true);
        echo " </br>
                    ";
        // line 25
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 25, $this->source); })()), "address", array()), "zipCity", array()), "city", array()), "html", null, true);
        echo " 
                </div>
            </li>
            <li>";
        // line 28
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 28, $this->source); })()), "description", array()), "html", null, true);
        echo " </li>
            <li>
                 ";
        // line 30
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "image", array(), "any", false, true), "url", array(), "any", true, true)) {
            // line 31
            echo "                     <img src=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl(((("uploads/img/" . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 31, $this->source); })()), "image", array()), "id", array())) . ".") . twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 31, $this->source); })()), "image", array()), "url", array()))), "html", null, true);
            echo "\" alt=\"";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 31, $this->source); })()), "image", array()), "alt", array()), "html", null, true);
            echo "\"> 
                 ";
        } else {
            // line 33
            echo "                     <img src=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("purple-unknown.png"), "html", null, true);
            echo "\"alt=\"Logo utilisateur\"> 
                 ";
        }
        // line 35
        echo "            </li>
        </ul>
        

        ";
        // line 39
        if ((twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 39, $this->source); })()), "hasReferent", array(0 => twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 39, $this->source); })()), "user", array())), "method") || ((isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 39, $this->source); })()) == twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 39, $this->source); })()), "user", array())))) {
            echo " 
            <a href=\"";
            // line 40
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_card_home", array("id" => twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 40, $this->source); })()), "id", array()))), "html", null, true);
            echo "\">Carte de sécurité Cairn</a>
            <a href=\"";
            // line 41
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_remove", array("id" => twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 41, $this->source); })()), "id", array()))), "html", null, true);
            echo "\">Fermer l'espace membre</a>

            <div class=\"accounts_tab\">
                ";
            // line 44
            echo twig_include($this->env, $context, "CairnUserBundle:Banking:accounts_table.html.twig", array("accounts" => (isset($context["accounts"]) || array_key_exists("accounts", $context) ? $context["accounts"] : (function () { throw new Twig_Error_Runtime('Variable "accounts" does not exist.', 44, $this->source); })())));
            echo "
            </div>

            ";
            // line 47
            if (twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 47, $this->source); })()), "hasReferent", array(0 => twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 47, $this->source); })()), "user", array())), "method")) {
                // line 48
                echo "                ";
                if ((twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 48, $this->source); })()), "enabled", array()) == true)) {
                    // line 49
                    echo "                    <a href=\"";
                    echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_block", array("id" => twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 49, $this->source); })()), "id", array()))), "html", null, true);
                    echo "\">Bloquer l'accès à la plateforme</a>
                ";
                } else {
                    // line 51
                    echo "                    <a href=\"";
                    echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_users_activate", array("id" => twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 51, $this->source); })()), "id", array()))), "html", null, true);
                    echo "\">Autoriser l'accès à la plateforme</a>
                ";
                }
                // line 53
                echo "
            ";
            }
            // line 55
            echo " 
            ";
            // line 56
            if ((twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 56, $this->source); })()), "id", array()) == twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 56, $this->source); })()), "user", array()), "id", array()))) {
                // line 57
                echo "                <a href=\"";
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_password_change");
                echo "\">Modifier son mot de passe</a>
            ";
            }
            // line 59
            echo "        ";
        }
        // line 60
        echo "

    </div>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Pro:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  220 => 60,  217 => 59,  211 => 57,  209 => 56,  206 => 55,  202 => 53,  196 => 51,  190 => 49,  187 => 48,  185 => 47,  179 => 44,  173 => 41,  169 => 40,  165 => 39,  159 => 35,  153 => 33,  145 => 31,  143 => 30,  138 => 28,  132 => 25,  128 => 24,  124 => 23,  118 => 20,  114 => 19,  110 => 18,  106 => 16,  104 => 15,  98 => 13,  89 => 12,  77 => 9,  72 => 8,  63 => 7,  46 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Pro/view.html.twig #}         
                                                                               
{% extends \"CairnUserBundle::layout.html.twig\" %}                        
                                                                               
{% block title %}{% endblock %}                                                                 
                                                                               
{%block stylesheets %}
    <link rel=\"stylesheet\" href=\"{{ asset('layout-style.css') }}\" type=\"text/css\" /> 
    <link rel=\"stylesheet\" href=\"{{ asset('pro.css') }}\" type=\"text/css\" /> 
{% endblock %}

{% block body %}
    {{parent()}} 
    
    {% set card = user.card %}
    <div class=\"profile_wrapper\">
        <ul>
            <li>{{ user.name }}</li>                
            <li>{{ user.username}} </li>
            <li>{{ user.email }}</li>
            <li>
                <div id=\"user_address\">                
                    {{ user.address.street}} </br>
                    {{ user.address.zipCity.zipCode}} </br>
                    {{ user.address.zipCity.city}} 
                </div>
            </li>
            <li>{{ user.description}} </li>
            <li>
                 {% if user.image.url is defined %}
                     <img src=\"{{asset('uploads/img/' ~ user.image.id ~ '.' ~ user.image.url)}}\" alt=\"{{user.image.alt}}\"> 
                 {% else %}
                     <img src=\"{{asset('purple-unknown.png')}}\"alt=\"Logo utilisateur\"> 
                 {% endif %}
            </li>
        </ul>
        

        {% if user.hasReferent(app.user) or user == app.user %} 
            <a href=\"{{path('cairn_user_card_home',{'id': user.id})}}\">Carte de sécurité Cairn</a>
            <a href=\"{{path('cairn_user_users_remove',{'id' : user.id})}}\">Fermer l'espace membre</a>

            <div class=\"accounts_tab\">
                {{include(\"CairnUserBundle:Banking:accounts_table.html.twig\",{'accounts':accounts})}}
            </div>

            {% if user.hasReferent(app.user) %}
                {% if user.enabled == true %}
                    <a href=\"{{path('cairn_user_users_block',{'id': user.id})}}\">Bloquer l'accès à la plateforme</a>
                {% else %}
                    <a href=\"{{path('cairn_user_users_activate',{'id': user.id})}}\">Autoriser l'accès à la plateforme</a>
                {% endif %}

            {% endif %}
 
            {% if user.id == app.user.id %}
                <a href=\"{{path('cairn_user_password_change')}}\">Modifier son mot de passe</a>
            {% endif %}
        {% endif %}


    </div>
{% endblock %}              


", "CairnUserBundle:Pro:view.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pro/view.html.twig");
    }
}

<?php

/* CairnUserBundle:User:index.html.twig */
class __TwigTemplate_4f5b6dcb62372605a75852c353dc45735802785be1bce44c6fcda50c89c79985 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:User:index.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:User:index.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:User:index.html.twig"));

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
        // line 14
        if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 14, $this->source); })()), "user", array()), "hasRole", array(0 => "ROLE_PRO"), "method")) {
            // line 15
            echo "         <h2> Espace Professionnel ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 15, $this->source); })()), "user", array()), "name", array()), "html", null, true);
            echo "</h2>                                                  
    ";
        } elseif (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source,         // line 16
(isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 16, $this->source); })()), "user", array()), "hasRole", array(0 => "ROLE_ADMIN"), "method")) {
            // line 17
            echo "         <h2> Espace Groupe Local ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 17, $this->source); })()), "user", array()), "name", array()), "html", null, true);
            echo "</h2>                                                  
    ";
        } else {
            // line 19
            echo "         <h2> Espace Administrateur ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 19, $this->source); })()), "user", array()), "name", array()), "html", null, true);
            echo "</h2>                                                  
    ";
        }
        // line 21
        echo "<h3> ";
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "d - m -Y"), "html", null, true);
        echo " </h3>                                                                               
<p>                                                                          

<div id=\"body_wrapper\">
    <div id=\"situation\">
        <a href=\"";
        // line 26
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_accounts_overview", array("id" => twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 26, $this->source); })()), "user", array()), "id", array()))), "html", null, true);
        echo "\">
            <span role=\"heading\" id=\"situation_title\"> situation </span>
        </a>
        <ul>
            ";
        // line 30
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["accounts"]) || array_key_exists("accounts", $context) ? $context["accounts"] : (function () { throw new Twig_Error_Runtime('Variable "accounts" does not exist.', 30, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["account"]) {
            // line 31
            echo "            <li class=\"accounts_info\">       
                <a href=\"";
            // line 32
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_account_operations", array("accountID" => twig_get_attribute($this->env, $this->source, $context["account"], "id", array()))), "html", null, true);
            echo "\">
                    <div>
                        ";
            // line 34
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "type", array()), "name", array()), "html", null, true);
            echo " </br>
                        ";
            // line 35
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["account"], "id", array()), "html", null, true);
            echo "
                    </div>
                    <span> ";
            // line 37
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["account"], "status", array()), "balance", array()), "html", null, true);
            echo " </span>
                </a>
            </li>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['account'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 41
        echo "        </ul>
    </div>
    <div id=\"operations\">
        <a href=\"#\">
            <span role=\"heading\" id=\"operations_title\"> Dernières opérations </span>
            <ul>
                ";
        // line 47
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["lastTransactions"]) || array_key_exists("lastTransactions", $context) ? $context["lastTransactions"] : (function () { throw new Twig_Error_Runtime('Variable "lastTransactions" does not exist.', 47, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["transaction"]) {
            // line 48
            echo "                <li class=\"transactions_info\">       
                    <div>
                        ";
            // line 50
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "date", array()), "html", null, true);
            echo " </br>
                        ";
            // line 51
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "description", array()), "html", null, true);
            echo "
                    </div>
                    <span> ";
            // line 53
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transaction"], "amount", array()), "html", null, true);
            echo " </span>
                </li>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transaction'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 56
        echo "            </ul>
        </a>
    </div>
    <div id=\"network\">
        <img src=\"";
        // line 60
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("purple-network.png"), "html", null, true);
        echo "\" alt=\"Network\">
        <span role=heading\" id=\"last_users_title\"> Ils ont rejoint votre réseau </span>
        <ul>
            ";
        // line 63
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["lastUsers"]) || array_key_exists("lastUsers", $context) ? $context["lastUsers"] : (function () { throw new Twig_Error_Runtime('Variable "lastUsers" does not exist.', 63, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["user"]) {
            // line 64
            echo "            <li class=\"users_show\">       
                <div>
                    <a href=\"";
            // line 66
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_profile_view", array("id" => twig_get_attribute($this->env, $this->source, $context["user"], "id", array()))), "html", null, true);
            echo "\">
                        ";
            // line 67
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["user"], "name", array()), "html", null, true);
            echo " </br>
                        ";
            // line 69
            echo "                    </a>
                </div>
            </li>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['user'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 73
        echo "        </ul>

    </div>

</div>

</p>                                                                         
                                                                            
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:User:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  243 => 73,  234 => 69,  230 => 67,  226 => 66,  222 => 64,  218 => 63,  212 => 60,  206 => 56,  197 => 53,  192 => 51,  188 => 50,  184 => 48,  180 => 47,  172 => 41,  162 => 37,  157 => 35,  153 => 34,  148 => 32,  145 => 31,  141 => 30,  134 => 26,  125 => 21,  119 => 19,  113 => 17,  111 => 16,  106 => 15,  104 => 14,  99 => 13,  90 => 12,  77 => 9,  72 => 8,  63 => 7,  46 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/User/index.html.twig #}         
                                                                               
{% extends \"CairnUserBundle::layout.html.twig\" %}                        
                                                                               
{% block title %}{% endblock %}                                                                 
                                                                               
{%block stylesheets %}
    <link rel=\"stylesheet\" href=\"{{ asset('layout-style.css') }}\" type=\"text/css\" /> 
    <link rel=\"stylesheet\" href=\"{{ asset('pro.css') }}\" type=\"text/css\" /> 

{% endblock %}
{% block body %}
    {{parent()}} 
    {% if app.user.hasRole('ROLE_PRO') %}
         <h2> Espace Professionnel {{ app.user.name }}</h2>                                                  
    {% elseif app.user.hasRole('ROLE_ADMIN') %}
         <h2> Espace Groupe Local {{ app.user.name }}</h2>                                                  
    {% else %}
         <h2> Espace Administrateur {{ app.user.name }}</h2>                                                  
    {% endif %}
<h3> {{ 'now'|date('d - m -Y') }} </h3>                                                                               
<p>                                                                          

<div id=\"body_wrapper\">
    <div id=\"situation\">
        <a href=\"{{path('cairn_user_banking_accounts_overview',{'id':app.user.id})}}\">
            <span role=\"heading\" id=\"situation_title\"> situation </span>
        </a>
        <ul>
            {% for account in accounts %}
            <li class=\"accounts_info\">       
                <a href=\"{{ path('cairn_user_banking_account_operations', {'accountID': account.id}) }}\">
                    <div>
                        {{account.type.name}} </br>
                        {{account.id}}
                    </div>
                    <span> {{account.status.balance}} </span>
                </a>
            </li>
            {% endfor %}
        </ul>
    </div>
    <div id=\"operations\">
        <a href=\"#\">
            <span role=\"heading\" id=\"operations_title\"> Dernières opérations </span>
            <ul>
                {% for transaction in lastTransactions %}
                <li class=\"transactions_info\">       
                    <div>
                        {{transaction.date}} </br>
                        {{transaction.description}}
                    </div>
                    <span> {{transaction.amount}} </span>
                </li>
                {% endfor %}
            </ul>
        </a>
    </div>
    <div id=\"network\">
        <img src=\"{{asset('purple-network.png')}}\" alt=\"Network\">
        <span role=heading\" id=\"last_users_title\"> Ils ont rejoint votre réseau </span>
        <ul>
            {% for user in lastUsers %}
            <li class=\"users_show\">       
                <div>
                    <a href=\"{{ path('cairn_user_profile_view', {'id': user.id}) }}\">
                        {{user.name}} </br>
                        {#<img src=\"{{asset('uploads/img/' ~ app.user.image.id ~ '.' ~ app.user.image.url)}}\" alt=\"{{app.user.image.alt}}\">#}
                    </a>
                </div>
            </li>
            {% endfor %}
        </ul>

    </div>

</div>

</p>                                                                         
                                                                            
{% endblock %}              


", "CairnUserBundle:User:index.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/User/index.html.twig");
    }
}

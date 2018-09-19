<?php

/* CairnUserBundle:Pro:list_beneficiaries.html.twig */
class __TwigTemplate_c490496741d89d984ee60c1c1ff5f072c13a8bf01b08d5d2c4a80c42e6fc3e50 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Pro:list_beneficiaries.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Pro:list_beneficiaries.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Pro:list_beneficiaries.html.twig"));

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
     <h2> Espace Professionnel ";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 14, $this->source); })()), "user", array()), "name", array()), "html", null, true);
        echo "</h2>                                                  
    <h3> ";
        // line 15
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "d - m -Y"), "html", null, true);
        echo " </h3>                                                                               

    <div class=\"body_wrapper\">
        <div id=\"table_upper_block\">
            <p> Mes bénéficiaires </p>
            <div class=\"a_bloc_actions\">
                <ul>    
                    <li><a href=\"";
        // line 22
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_beneficiaries_add");
        echo "\">Ajouter </a></li>
                </ul>
            </div>
        </div>
        <table>                                                                    
             <thead>                                                                    
                 <tr>                                                                   
                     <th> Nom </th>                                                    
                     <th> Coordonnées de compte</th>                                          
                     <th> Actions </th>                                                 
                 </tr>                                                                  
             </thead>                                                                   
                                                                                        
             <tbody>                                                                    
             ";
        // line 36
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["beneficiaries"]) || array_key_exists("beneficiaries", $context) ? $context["beneficiaries"] : (function () { throw new Twig_Error_Runtime('Variable "beneficiaries" does not exist.', 36, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["beneficiary"]) {
            echo "                                      
                 <tr>                                                                   
                     <td>";
            // line 38
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["beneficiary"], "user", array()), "name", array()), "html", null, true);
            echo " </td>                                    
                     <td>";
            // line 39
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["beneficiary"], "ICC", array()), "html", null, true);
            echo "</td>                                                              
                     <td><a href=\"";
            // line 40
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_beneficiaries_remove", array("id" => twig_get_attribute($this->env, $this->source, $context["beneficiary"], "id", array()))), "html", null, true);
            echo "\"> Supprimer</a> 
                         |
                         <a href=\"";
            // line 42
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_beneficiaries_edit", array("id" => twig_get_attribute($this->env, $this->source, $context["beneficiary"], "id", array()))), "html", null, true);
            echo "\"> Modifier</a>           
                    </td>                                 
                 </tr>                                                                  
             ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 46
            echo "                Pas encore de bénéficiaire !
             ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['beneficiary'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 47
        echo "                                                               
             </tbody>                                                                   
        </table>    
    </div>
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Pro:list_beneficiaries.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  171 => 47,  164 => 46,  155 => 42,  150 => 40,  146 => 39,  142 => 38,  134 => 36,  117 => 22,  107 => 15,  103 => 14,  98 => 13,  89 => 12,  77 => 9,  72 => 8,  63 => 7,  46 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Pro/list_beneficiaries.html.twig #} 
                                                                               
{% extends \"CairnUserBundle::layout.html.twig\" %}                        
                                                                               
{% block title %}{% endblock %}                                                                 
                                                                               
{%block stylesheets %}
    <link rel=\"stylesheet\" href=\"{{ asset('layout-style.css') }}\" type=\"text/css\" /> 
    <link rel=\"stylesheet\" href=\"{{ asset('pro.css') }}\" type=\"text/css\" /> 
{% endblock %}

{% block body %}
    {{parent()}} 
     <h2> Espace Professionnel {{ app.user.name }}</h2>                                                  
    <h3> {{ 'now'|date('d - m -Y') }} </h3>                                                                               

    <div class=\"body_wrapper\">
        <div id=\"table_upper_block\">
            <p> Mes bénéficiaires </p>
            <div class=\"a_bloc_actions\">
                <ul>    
                    <li><a href=\"{{path('cairn_user_beneficiaries_add')}}\">Ajouter </a></li>
                </ul>
            </div>
        </div>
        <table>                                                                    
             <thead>                                                                    
                 <tr>                                                                   
                     <th> Nom </th>                                                    
                     <th> Coordonnées de compte</th>                                          
                     <th> Actions </th>                                                 
                 </tr>                                                                  
             </thead>                                                                   
                                                                                        
             <tbody>                                                                    
             {% for beneficiary in beneficiaries %}                                      
                 <tr>                                                                   
                     <td>{{ beneficiary.user.name}} </td>                                    
                     <td>{{ beneficiary.ICC}}</td>                                                              
                     <td><a href=\"{{path('cairn_user_beneficiaries_remove', {'id' : beneficiary.id})}}\"> Supprimer</a> 
                         |
                         <a href=\"{{path('cairn_user_beneficiaries_edit', {'id' : beneficiary.id})}}\"> Modifier</a>           
                    </td>                                 
                 </tr>                                                                  
             {% else %}
                Pas encore de bénéficiaire !
             {% endfor %}                                                               
             </tbody>                                                                   
        </table>    
    </div>
{% endblock %}              


", "CairnUserBundle:Pro:list_beneficiaries.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Pro/list_beneficiaries.html.twig");
    }
}

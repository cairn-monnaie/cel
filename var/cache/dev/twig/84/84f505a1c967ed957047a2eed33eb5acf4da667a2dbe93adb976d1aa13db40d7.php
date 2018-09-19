<?php

/* CairnUserBundle:Registration:index.html.twig */
class __TwigTemplate_2c15f56600e02af4a2dfb4b8a63da4b173c832e6403a85625a2cb935fb82c059 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Registration:index.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Registration:index.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Registration:index.html.twig"));

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
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_ADMIN")) {
            // line 7
            echo "        <h1> Qui est-il ?</h1>

        ";
            // line 9
            if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
                // line 10
                echo "            <li><a href=\"";
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration", array("type" => "superAdmin"));
                echo "\" > Administrateur </a></li>
            <li><a href=\"";
                // line 11
                echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration", array("type" => "localGroup"));
                echo "\" > Groupe local </a></li>
        ";
            }
            // line 13
            echo "        <li><a href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration", array("type" => "pro"));
            echo "\" > Professionnel </a></li>
        <li><a href=\"";
            // line 14
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration", array("type" => "adherent"));
            echo "\" > Particulier </a></li>

    ";
        }
        // line 17
        echo "
    ";
        // line 18
        if ( !$this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("IS_AUTHENTICATED_REMEMBERED")) {
            // line 19
            echo "        <h1> Qui êtes-vous ? </h1>
            <div>
                <ul>
                    <li><a href=\"";
            // line 22
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration", array("type" => "pro"));
            echo "\" > Professionnel </a></li>
                    <li><a href=\"";
            // line 23
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_registration", array("type" => "adherent"));
            echo "\" > Particulier </a></li>
                </ul>
            </div>
    ";
        }
        // line 27
        echo "         
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Registration:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  104 => 27,  97 => 23,  93 => 22,  88 => 19,  86 => 18,  83 => 17,  77 => 14,  72 => 13,  67 => 11,  62 => 10,  60 => 9,  56 => 7,  53 => 6,  44 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Registration/index.html.twig #}

{% extends \"CairnUserBundle::layout.html.twig\" %}

{% block body %}
    {% if is_granted('ROLE_ADMIN') %}
        <h1> Qui est-il ?</h1>

        {% if is_granted('ROLE_SUPER_ADMIN') %}
            <li><a href=\"{{path('cairn_user_registration',{type:'superAdmin'} ) }}\" > Administrateur </a></li>
            <li><a href=\"{{path('cairn_user_registration',{type:'localGroup'} ) }}\" > Groupe local </a></li>
        {% endif %}
        <li><a href=\"{{path('cairn_user_registration',{type:'pro'} ) }}\" > Professionnel </a></li>
        <li><a href=\"{{path('cairn_user_registration',{type:'adherent'} ) }}\" > Particulier </a></li>

    {% endif %}

    {% if not is_granted('IS_AUTHENTICATED_REMEMBERED') %}
        <h1> Qui êtes-vous ? </h1>
            <div>
                <ul>
                    <li><a href=\"{{path('cairn_user_registration',{type:'pro'} ) }}\" > Professionnel </a></li>
                    <li><a href=\"{{path('cairn_user_registration',{type:'adherent'} ) }}\" > Particulier </a></li>
                </ul>
            </div>
    {% endif %}
         
{% endblock %}
", "CairnUserBundle:Registration:index.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Registration/index.html.twig");
    }
}

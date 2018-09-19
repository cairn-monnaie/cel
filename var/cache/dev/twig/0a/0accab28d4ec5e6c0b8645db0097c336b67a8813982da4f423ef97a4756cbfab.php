<?php

/* CairnUserBundle:Banking:accounts_overview.html.twig */
class __TwigTemplate_2c92972c2cf1d52f42b1c41382f79f1c5c02cd46b527d1700a4f7e7ec2ad6b8c extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:accounts_overview.html.twig", 3);
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:accounts_overview.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:accounts_overview.html.twig"));

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

    ";
        // line 8
        if ((twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 8, $this->source); })()), "hasRole", array(0 => "ROLE_SUPER_ADMIN"), "method") || twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 8, $this->source); })()), "hasRole", array(0 => "ROLE_PRO"), "method"))) {
            // line 9
            echo "    <div> <img src=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("download_mini.png"), "html", null, true);
            echo "\" alt=\"logo_download\">
                <a href=\"";
            // line 10
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_accounts_overview_download");
            echo "\">Télécharger relevé de compte</a>
    </div>
    ";
        }
        // line 13
        echo "    <div class=\"body_wrapper\">


        ";
        // line 16
        echo twig_include($this->env, $context, "CairnUserBundle:Banking:accounts_table.html.twig", array("accounts" => (isset($context["accounts"]) || array_key_exists("accounts", $context) ? $context["accounts"] : (function () { throw new Twig_Error_Runtime('Variable "accounts" does not exist.', 16, $this->source); })())));
        echo "
    </div>

";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:accounts_overview.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  77 => 16,  72 => 13,  66 => 10,  61 => 9,  59 => 8,  53 => 6,  44 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/accounts_overview.html.twig #}

{% extends \"CairnUserBundle::layout.html.twig\" %}

{% block body %}
    {{parent()}}

    {% if user.hasRole('ROLE_SUPER_ADMIN') or user.hasRole('ROLE_PRO') %}
    <div> <img src=\"{{ asset('download_mini.png') }}\" alt=\"logo_download\">
                <a href=\"{{path('cairn_user_banking_accounts_overview_download') }}\">Télécharger relevé de compte</a>
    </div>
    {% endif %}
    <div class=\"body_wrapper\">


        {{include(\"CairnUserBundle:Banking:accounts_table.html.twig\",{'accounts':accounts})}}
    </div>

{% endblock %}

", "CairnUserBundle:Banking:accounts_overview.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/accounts_overview.html.twig");
    }
}

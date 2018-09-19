<?php

/* CairnUserBundle:Banking:account_download_options.html.twig */
class __TwigTemplate_cda50e871f6d7181c5d2d8a28c3218ff0d8a462432569cca58a4bad1b35cd270 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:account_download_options.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Banking:account_download_options.html.twig"));

        // line 2
        echo "
    <div>
        <img src=\"";
        // line 4
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("purple-download.png"), "html", null, true);
        echo "\" alt=\"logo_download\">
        <ul>
            <a href=\"";
        // line 6
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_rib_download", array("id" => twig_get_attribute($this->env, $this->source, (isset($context["account"]) || array_key_exists("account", $context) ? $context["account"] : (function () { throw new Twig_Error_Runtime('Variable "account" does not exist.', 6, $this->source); })()), "id", array()))), "html", null, true);
        echo "\"> RIB Cairn </a>

            ";
        // line 8
        if ( !twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new Twig_Error_Runtime('Variable "app" does not exist.', 8, $this->source); })()), "user", array()), "hasRole", array(0 => "ROLE_ADMIN"), "method")) {
            // line 9
            echo "                <a href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_accounts_overview_download");
            echo "\"> Relevé de compte </a>
            ";
        }
        // line 11
        echo "        </ul>


    </div>

";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:account_download_options.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  51 => 11,  45 => 9,  43 => 8,  38 => 6,  33 => 4,  29 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Banking/account_download_options.html.twig #}

    <div>
        <img src=\"{{ asset('purple-download.png') }}\" alt=\"logo_download\">
        <ul>
            <a href=\"{{path('cairn_user_banking_rib_download',{'id':account.id})}}\"> RIB Cairn </a>

            {% if not app.user.hasRole('ROLE_ADMIN') %}
                <a href=\"{{path('cairn_user_banking_accounts_overview_download')}}\"> Relevé de compte </a>
            {% endif %}
        </ul>


    </div>

", "CairnUserBundle:Banking:account_download_options.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/account_download_options.html.twig");
    }
}

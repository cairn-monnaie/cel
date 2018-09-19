<?php

/* CairnUserBundle:Banking:account_download_options.html.twig */
class __TwigTemplate_b067a6bf2277a15b1a030f2a58ec37f5cc6721fb5ca85c56bdadb6de8dda35da extends Twig_Template
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
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_rib_download", array("id" => twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "id", array()))), "html", null, true);
        echo "\"> RIB Cairn </a>

            ";
        // line 8
        if ( !twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()), "hasRole", array(0 => "ROLE_ADMIN"), "method")) {
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
        return array (  45 => 11,  39 => 9,  37 => 8,  32 => 6,  27 => 4,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:account_download_options.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/account_download_options.html.twig");
    }
}

<?php

/* CairnUserBundle:Banking:accounts_overview.html.twig */
class __TwigTemplate_3bc718a15e78852bce6a9715f27a4b60ecde2c77e4f8708a1a501400c6f186b5 extends Twig_Template
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
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_body($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "

    ";
        // line 8
        if ((twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "hasRole", array(0 => "ROLE_SUPER_ADMIN"), "method") || twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "hasRole", array(0 => "ROLE_PRO"), "method"))) {
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
        echo twig_include($this->env, $context, "CairnUserBundle:Banking:accounts_table.html.twig", array("accounts" => ($context["accounts"] ?? null)));
        echo "
    </div>

";
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
        return array (  59 => 16,  54 => 13,  48 => 10,  43 => 9,  41 => 8,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:accounts_overview.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/accounts_overview.html.twig");
    }
}

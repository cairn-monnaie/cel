<?php

/* CairnUserBundle:Banking:conversion_operations.html.twig */
class __TwigTemplate_7eb70f4e8b2c2a6ab5b450baff2bc92f7d02c1c7588524c79d2d7860f2a6b981 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:conversion_operations.html.twig", 3);
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

    <div class=\"body_wrapper>
        <div id=\"conversion_action\">
            <h1> Effectuer une conversion </h1>
           <ul>
                ";
        // line 12
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
            // line 13
            echo "                    <li><a href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_conversion_request", array("to" => "self"));
            echo "\"> Vers un compte de l'Association</a></li>
                    <li><a href=\"";
            // line 14
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_conversion_request", array("to" => "other"));
            echo "\"> Vers un professionnel </a></li> 
                ";
        } else {
            // line 16
            echo "                     <li><a href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_conversion_request", array("to" => "self"));
            echo "\"> Effectuer une conversion</a></li>
                ";
        }
        // line 18
        echo "
           <ul> 
        </div>
        <div id=\"conversion_management\">
            <h1> Gérer les conversions </h1>
            <ul>
                <li><a href=\"";
        // line 24
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations_view", array("frequency" => "unique", "type" => "conversion")), "html", null, true);
        echo "\"> Voir mes conversions </a></li>
            </ul>
        </div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:conversion_operations.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 24,  63 => 18,  57 => 16,  52 => 14,  47 => 13,  45 => 12,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:conversion_operations.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/conversion_operations.html.twig");
    }
}

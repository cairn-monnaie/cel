<?php

/* CairnUserBundle:Banking:reconversion_operations.html.twig */
class __TwigTemplate_8a3abd0a619805c2f0148f50a9011504c6505325f2d5fd770ac47d9c79a91da6 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:reconversion_operations.html.twig", 3);
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
        <div id=\"reconversion_action\">
            <h1> Les reconversions </h1>
           <ul>
                <li><a href=\"";
        // line 12
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_reconversion_request");
        echo "\"> Effectuer une reconversion     </a> </li>
           <ul> 
        </div>
        <div id=\"reconversion_management\">
            <h1> Gérer les reconversions </h1>
            <ul>
                <li><a href=\"";
        // line 18
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations_view", array("frequency" => "unique", "type" => "reconversion")), "html", null, true);
        echo "\"> Voir mes reconversions </a></li>
            </ul>
        </div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:reconversion_operations.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  54 => 18,  45 => 12,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:reconversion_operations.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/reconversion_operations.html.twig");
    }
}

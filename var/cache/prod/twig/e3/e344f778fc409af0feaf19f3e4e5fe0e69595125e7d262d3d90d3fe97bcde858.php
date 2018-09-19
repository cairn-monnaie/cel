<?php

/* CairnUserBundle:Banking:deposit_operations.html.twig */
class __TwigTemplate_2778b6cd26ca69afd67b8430715dad7255149947a0829313f77ee256c0359bfd extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:deposit_operations.html.twig", 3);
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
        <div id=\"deposit_action\">
            <h1> Les dépôts </h1>
           <ul>
                <li><a href=\"";
        // line 12
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_deposit_request");
        echo "\"> Effectuer un dépôt     </a> </li>
               ";
        // line 14
        echo "
           <ul> 
        </div>
        <div id=\"deposit_management\">
            <h1> Gérer les dépôts </h1>
            <ul>
                <li><a href=\"";
        // line 20
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations_view", array("frequency" => "unique", "type" => "deposit")), "html", null, true);
        echo "\"> Voir les dépôts </a></li>
                <li><a href=\"";
        // line 21
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_beneficiaries_list");
        echo "\"> Mes favoris </a></li>
            </ul>
        </div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:deposit_operations.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  61 => 21,  57 => 20,  49 => 14,  45 => 12,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:deposit_operations.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/deposit_operations.html.twig");
    }
}

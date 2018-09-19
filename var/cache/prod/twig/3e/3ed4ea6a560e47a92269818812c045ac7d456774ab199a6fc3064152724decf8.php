<?php

/* CairnUserBundle:Banking:transaction_operations.html.twig */
class __TwigTemplate_a4f1a6bd0758a1c0e0d3b07445b520f85266b696c8fc5db9dff82abbf7e6ac2a extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:transaction_operations.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
            'javascripts' => array($this, 'block_javascripts'),
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
        <div id=\"transaction_action\">
            <h1> Effectuer un virement </h1>
           <ul>
                <li><a href=\"";
        // line 12
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_to", array("frequency" => "unique"));
        echo "\"> Virement unique     </a></li>
                <li><a href=\"";
        // line 13
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_to", array("frequency" => "recurring"));
        echo "\"> Virement permanent </a></li>

                ";
        // line 19
        echo "           <ul> 
        </div>
        <div id=\"transfer_management\">
            <h1> Gérer ses virements </h1>
            <ul>
                <li><a href=\"";
        // line 24
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations_view", array("frequency" => "unique", "type" => "transaction")), "html", null, true);
        echo "\"> Virements uniques </a></li>
                <li><a href=\"";
        // line 25
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_operations_view", array("frequency" => "recurring", "type" => "transaction")), "html", null, true);
        echo "\"> Virements permanents </a></li>
                ";
        // line 26
        if ( !$this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_ADMIN")) {
            // line 27
            echo "                    <li><a href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_beneficiaries_list");
            echo "\"> Mes bénéficiaires </a></li>
                ";
        }
        // line 29
        echo "            </ul>
        </div>
";
    }

    // line 33
    public function block_javascripts($context, array $blocks = array())
    {
        // line 34
        echo "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js\"></script>
    <script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.12/jquery-ui.min.js\"></script>

<script>
jQuery(function (\$) {
    ;    
});
</script>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:transaction_operations.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  87 => 34,  84 => 33,  78 => 29,  72 => 27,  70 => 26,  66 => 25,  62 => 24,  55 => 19,  50 => 13,  46 => 12,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:transaction_operations.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/transaction_operations.html.twig");
    }
}

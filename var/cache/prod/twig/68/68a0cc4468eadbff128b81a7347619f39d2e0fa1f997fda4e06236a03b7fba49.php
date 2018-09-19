<?php

/* CairnUserBundle:Banking:transaction_to.html.twig */
class __TwigTemplate_cc010d1ceab6961f99009111898a4ba5a374aa6f2fc3f74709b0add2b73d5b3d extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Banking:transaction_to.html.twig", 3);
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
        <div id=\"transaction_to\">
           <ul>
                <li><a href=\"";
        // line 11
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_request", array("frequency" => ($context["frequency"] ?? null), "to" => "self")), "html", null, true);
        echo "\"> Entre vos comptes     </a></li>
                <li><a href=\"";
        // line 12
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_request", array("frequency" => ($context["frequency"] ?? null), "to" => "beneficiary")), "html", null, true);
        echo "\"> Vers un bénéficiaire enregistré </a></li>

                <li><a href=\"";
        // line 14
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banking_transaction_request", array("frequency" => ($context["frequency"] ?? null), "to" => "new")), "html", null, true);
        echo "\"> Vers un nouveau bénéficiaire </a></li>
           <ul> 
        </div>
            </ul>
        </div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banking:transaction_to.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  53 => 14,  48 => 12,  44 => 11,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banking:transaction_to.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banking/transaction_to.html.twig");
    }
}

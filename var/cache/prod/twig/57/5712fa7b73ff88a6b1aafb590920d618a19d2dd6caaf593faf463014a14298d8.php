<?php

/* CairnUserBundle:BankConnection:view_deposit.html.twig */
class __TwigTemplate_8987b4d4e07e19cf7b90057cf394a7620a117c4426748289249f023598b1344e extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserBundle:BankConnection:view_deposit.html.twig", 3);
        $this->blocks = array(
            'fos_user_content' => array($this, 'block_fos_user_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserCyclosBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_fos_user_content($context, array $blocks = array())
    {
        // line 6
        echo "
<h3>Dépôt</h3>

<div class=\"well\">
<li> ";
        // line 10
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["deposit"] ?? null), "nbCairns", array()), "html", null, true);
        echo " </li>
<li> ";
        // line 11
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["deposit"] ?? null), "nbEuros", array()), "html", null, true);
        echo "  </li>
<li> ";
        // line 12
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["deposit"] ?? null), "exchangeOffice", array()), "name", array()), "html", null, true);
        echo " </li>
</div>
    <a href=\"";
        // line 14
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_bankconnection_deposit_edit", array("id" => twig_get_attribute($this->env, $this->source, ($context["deposit"] ?? null), "id", array()))), "html", null, true);
        echo "\">
          Modifier le dépôt   
        </a>

";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:BankConnection:view_deposit.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  54 => 14,  49 => 12,  45 => 11,  41 => 10,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:BankConnection:view_deposit.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/BankConnection/view_deposit.html.twig");
    }
}

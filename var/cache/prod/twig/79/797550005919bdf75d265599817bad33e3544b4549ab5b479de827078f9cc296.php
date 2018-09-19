<?php

/* CairnUserCyclosBundle:Config/Account:index.html.twig */
class __TwigTemplate_e6a2dc0f97b68b492f1055e3f192011197d6668c121c475015ab8638c2bd0703 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/Account:index.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
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

    // line 6
    public function block_body($context, array $blocks = array())
    {
        // line 7
        echo "   ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "

  <h2>Gérer les paramètres des comptes</h2>

  ";
        // line 15
        echo "
    <a href=\"";
        // line 16
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_home");
        echo "\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-edit\"></i>
      Gérer les types de comptes
    </a>

";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/Account:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 16,  43 => 15,  35 => 7,  32 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/Account:index.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/Account/index.html.twig");
    }
}

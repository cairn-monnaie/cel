<?php

/* CairnUserCyclosBundle:Config/TransferType:view.html.twig */
class __TwigTemplate_1c3b76ec32b07fcced227f62ae1e7720d614a1c2d8fb0520d2ffac8e6eaa9392 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/TransferType:view.html.twig", 3);
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
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "
    <h2> Type de transfert </h2>
    <div class=\"well\">
        <h3> ";
        // line 10
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transferType"] ?? null), "from", array()), "name", array()), "html", null, true);
        echo " -----> ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transferType"] ?? null), "to", array()), "name", array()), "html", null, true);
        echo "</li>
        <li> virements automatiques autorisés : ";
        // line 11
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["transferType"] ?? null), "allowsRecurringPayments", array()), "html", null, true);
        echo "</li>
        <li> Actif : ";
        // line 12
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["transferType"] ?? null), "enabled", array()), "html", null, true);
        echo " </li>
        
      ";
        // line 28
        echo "  </div>

  <p>
     <a href=\"";
        // line 31
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_transfertype_edit", array("id" => twig_get_attribute($this->env, $this->source, ($context["transferType"] ?? null), "id", array()))), "html", null, true);
        echo "\" >
      Mettre à jour le type de transfert
     </a>
  </p>

";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/TransferType:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 31,  57 => 28,  52 => 12,  48 => 11,  42 => 10,  35 => 7,  32 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/TransferType:view.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/TransferType/view.html.twig");
    }
}

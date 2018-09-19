<?php

/* CairnUserCyclosBundle:Config/Network:view.html.twig */
class __TwigTemplate_947fc0640ca5bb571b72c29689cdfe79ed4f3aa1f260fe07bd9ac97d571c1c9e extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/Network:view.html.twig", 3);
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
    ";
        // line 8
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
            // line 9
            echo "  <h2>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["network"] ?? null), "name", array()), "html", null, true);
            echo "</h2>

  <div class=\"well\">
    ";
            // line 12
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["network"] ?? null), "name", array()), "html", null, true);
            echo "
  </div>
    ";
        }
        // line 15
        echo "  <p>
    ";
        // line 16
        if ($this->extensions['Symfony\Bridge\Twig\Extension\SecurityExtension']->isGranted("ROLE_SUPER_ADMIN")) {
            // line 17
            echo "       ";
            // line 20
            echo "
        <a href=\"";
            // line 21
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_home");
            echo "\" >
        Gestion des comptes
        </a>

    ";
        }
        // line 26
        echo "  </p>

";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/Network:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 26,  65 => 21,  62 => 20,  60 => 17,  58 => 16,  55 => 15,  49 => 12,  42 => 9,  40 => 8,  35 => 7,  32 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/Network:view.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/Network/view.html.twig");
    }
}

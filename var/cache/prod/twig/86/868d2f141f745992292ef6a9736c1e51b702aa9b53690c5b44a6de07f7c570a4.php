<?php

/* CairnUserCyclosBundle::view.html.twig */
class __TwigTemplate_939690e4077ef181dbbfa23a9074f08fb6092a9529de572a83adfe5ce1fcaa56 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle::view.html.twig", 3);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'cairnusercyclos_body' => array($this, 'block_cairnusercyclos_body'),
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
    public function block_title($context, array $blocks = array())
    {
        // line 6
        echo "  Réseau du Cairn - ";
        $this->displayParentBlock("title", $context, $blocks);
        echo "
";
    }

    // line 9
    public function block_cairnusercyclos_body($context, array $blocks = array())
    {
        // line 10
        echo "
  <h2>";
        // line 11
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["network"] ?? null), "name", array()), "html", null, true);
        echo "</h2>

  <p>
    <a href=\"";
        // line 14
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_home");
        echo "\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-chevron-left\"></i>
      Retour à la liste des réseaux
    </a>
    <a href=\"";
        // line 18
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_network_edit", array("name" => twig_get_attribute($this->env, $this->source, ($context["network"] ?? null), "name", array()))), "html", null, true);
        echo "\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-edit\"></i>
      Mettre à jour le réseau
    </a>
  </p>

";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle::view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 18,  55 => 14,  49 => 11,  46 => 10,  43 => 9,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle::view.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/view.html.twig");
    }
}

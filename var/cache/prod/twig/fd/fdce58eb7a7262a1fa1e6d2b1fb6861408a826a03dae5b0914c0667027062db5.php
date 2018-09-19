<?php

/* CairnUserBundle:Card:card_operation.html.twig */
class __TwigTemplate_42a1823af16151a891885195d88af088ac667f97e53334dfe4018a5385eb0f65 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Card:card_operation.html.twig", 3);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
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
    public function block_title($context, array $blocks = array())
    {
    }

    // line 7
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 8
        echo "    <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("layout-style.css"), "html", null, true);
        echo "\" type=\"text/css\" /> 
    <link rel=\"stylesheet\" href=\"";
        // line 9
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("pro.css"), "html", null, true);
        echo "\" type=\"text/css\" /> 

";
    }

    // line 12
    public function block_body($context, array $blocks = array())
    {
        // line 13
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo " 

     <a href=\"";
        // line 15
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_card_revoke", array("id" => twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "id", array()))), "html", null, true);
        echo "\"> Révoquer la carte </a>
     <a href=\"";
        // line 16
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_card_new", array("id" => twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "id", array()))), "html", null, true);
        echo "\"> Commander une carte </a>

    ";
        // line 18
        if ((($context["user"] ?? null) == twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array()))) {
            // line 19
            echo "        <a href=\"";
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_card_validate");
            echo "\"> Activer la carte </a>
    ";
        }
        // line 21
        echo "
    ";
        // line 22
        if (twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "hasReferent", array(0 => twig_get_attribute($this->env, $this->source, ($context["app"] ?? null), "user", array())), "method")) {
            // line 23
            echo "         <a href=\"";
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_card_generate", array("id" => twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "id", array()))), "html", null, true);
            echo "\"> Générer la carte </a>
    ";
        }
        // line 25
        echo "
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Card:card_operation.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  91 => 25,  85 => 23,  83 => 22,  80 => 21,  74 => 19,  72 => 18,  67 => 16,  63 => 15,  57 => 13,  54 => 12,  47 => 9,  42 => 8,  39 => 7,  34 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Card:card_operation.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Card/card_operation.html.twig");
    }
}

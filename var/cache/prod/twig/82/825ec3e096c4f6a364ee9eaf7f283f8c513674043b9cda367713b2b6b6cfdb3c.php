<?php

/* CairnUserBundle:Card:validate_card.html.twig */
class __TwigTemplate_a183423652ef274ccc3d87ab01acb28e1b4ab62c8e7ff9be33d1ebcd43e77127 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Card:validate_card.html.twig", 3);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
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
    
    <h2>Valider votre carte de sécurité Cairn n° ";
        // line 15
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["card"] ?? null), "number", array()), "html", null, true);
        echo " </h2>
    <h3>Saisir une clé </h3>
    <div>                                                                          
        ";
        // line 18
        echo twig_include($this->env, $context, "CairnUserBundle:Card:warning_card_tries.html.twig", array("card" => ($context["card"] ?? null)));
        echo "
        Clé contenue dans la case numérotée <strong>";
        // line 19
        echo twig_escape_filter($this->env, ($context["position"] ?? null), "html", null, true);
        echo "</strong> de votre carte n° ";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["card"] ?? null), "number", array()), "html", null, true);
        echo "
        ";
        // line 20
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_start');
        echo "
        ";
        // line 21
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_end');
        echo "
    </div>                                                                         

";
    }

    // line 26
    public function block_javascripts($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Card:validate_card.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  92 => 26,  84 => 21,  80 => 20,  74 => 19,  70 => 18,  64 => 15,  58 => 13,  55 => 12,  48 => 9,  43 => 8,  40 => 7,  35 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Card:validate_card.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Card/validate_card.html.twig");
    }
}

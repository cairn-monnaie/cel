<?php

/* CairnUserBundle:Emails:confirm_user.html.twig */
class __TwigTemplate_9794bf19f8266df3c89cb6eaf8344390418133e20f9a09b5f21437385ca1c0a9 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Emails:confirm_user.html.twig", 3);
        $this->blocks = array(
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
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 6
        echo "    <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("layout-style.css"), "html", null, true);
        echo "\" type=\"text/css\" /> 
    <link rel=\"stylesheet\" href=\"";
        // line 7
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("pro.css"), "html", null, true);
        echo "\" type=\"text/css\" /> 
";
    }

    // line 10
    public function block_body($context, array $blocks = array())
    {
        // line 11
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo " 

<h3>Finalisation de l'inscription </h3>

<div class=\"well\">
  ";
        // line 16
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form');
        echo "
</div>

";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:confirm_user.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  59 => 16,  50 => 11,  47 => 10,  41 => 7,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:confirm_user.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/confirm_user.html.twig");
    }
}

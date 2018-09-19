<?php

/* CairnUserBundle:Security:new_password.html.twig */
class __TwigTemplate_cfc0c3dacdcebc9c8e8341153183a5ae31769a652140589acda411f66b2996ab extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Security:new_password.html.twig", 3);
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
    
    <h2>Nouveau mot de passe </h2>
    <h3>Saisir votre identifiant </h3>
    <div>                                                                          
        ";
        // line 18
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_start');
        echo "
        ";
        // line 19
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_end');
        echo "
    </div>                                                                         

";
    }

    // line 24
    public function block_javascripts($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Security:new_password.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  79 => 24,  71 => 19,  67 => 18,  58 => 13,  55 => 12,  48 => 9,  43 => 8,  40 => 7,  35 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Security:new_password.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Security/new_password.html.twig");
    }
}

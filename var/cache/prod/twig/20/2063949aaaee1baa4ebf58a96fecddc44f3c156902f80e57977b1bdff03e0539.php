<?php

/* CairnUserBundle:Registration:register_adherent_content.html.twig */
class __TwigTemplate_a67b0c3dff2a2186ec544b32f75a4ac56aa1eb6bd77d30ccab80695ecf3559ee extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserBundle::layout.html.twig", "CairnUserBundle:Registration:register_adherent_content.html.twig", 3);
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
        echo "    <h1> Inscription impossible </h1>
        La création de compte n'est pour l'instant possible que pour les professionnels du réseau !
        
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Registration:register_adherent_content.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Registration:register_adherent_content.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Registration/register_adherent_content.html.twig");
    }
}

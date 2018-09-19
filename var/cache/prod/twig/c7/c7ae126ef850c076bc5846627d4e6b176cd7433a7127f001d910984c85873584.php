<?php

/* CairnUserBundle:Emails:maintenance.html.twig */
class __TwigTemplate_fb0ee92e6d9bdea4d806e1d0ff45ef3ae4914d243e9d75af49b2cdddca8c8c77 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 2
        echo "
La plateforme est en maintenance pour un délai indéterminé. Veuillez nous excuser. 


A très bientôt,

Le Cairn,
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:maintenance.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:maintenance.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/maintenance.html.twig");
    }
}

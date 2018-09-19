<?php

/* CairnUserBundle:Emails:open_access.html.twig */
class __TwigTemplate_169edcebfd423fd032fc14bc1a0258e796d7ebd2fa54a0f7f16fe30a023c405b extends Twig_Template
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
La plateforme est à nouveau accessible à tous les professionnels.  

A très bientôt,

Le Cairn,
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:open_access.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:open_access.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/open_access.html.twig");
    }
}

<?php

/* CairnUserBundle:Emails:expiration_card.html.twig */
class __TwigTemplate_74f311ab568739f1f6afe41dd8fb2782ca5e2460eb4dcf57eeebe5c1d5f57d57 extends Twig_Template
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
    Vous n'avez pas validé votre carte de sécurité Cairn à temps.
    Pour des raisons de sécurité, elle a donc été automatiquement révoquée. Vous ne pourrez pas effectuer les opérations sensibles.

    Vous pouvez en commander une nouvelle sur la plateforme.

    Le Cairn,
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:expiration_card.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:expiration_card.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/expiration_card.html.twig");
    }
}

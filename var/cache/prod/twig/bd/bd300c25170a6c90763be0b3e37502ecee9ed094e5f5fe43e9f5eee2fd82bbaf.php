<?php

/* CairnUserBundle:Emails:email_expiration.html.twig */
class __TwigTemplate_e91db5c0ca030feb9af59ccab948c5a39e653adf0c609aea253634ed505d601f extends Twig_Template
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
Votre adresse email n'a pas été validée dans les délais. Votre espace membre Cairn a donc été automatiquement supprimé.

En espérant vous compter bientôt parmi nous,

Le Cairn,
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:email_expiration.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:email_expiration.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/email_expiration.html.twig");
    }
}

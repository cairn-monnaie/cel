<?php

/* CairnUserBundle:Emails:pending_validation.html.twig */
class __TwigTemplate_defb12df5dd750248547667f1629fbaf82d844eb3adf448ec8644ffc91906ce0 extends Twig_Template
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
Merci d'avoir confirmé la validité de votre adresse email.
Les différentes informations liées à votre activité sur la plateforme vous parviendront à cette adresse.

Un email vous sera envoyé dès lors que l'équipe administrative aura validé votre inscription.
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:pending_validation.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:pending_validation.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/pending_validation.html.twig");
    }
}

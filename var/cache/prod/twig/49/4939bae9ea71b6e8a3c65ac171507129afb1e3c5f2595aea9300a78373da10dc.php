<?php

/* CairnUserBundle:Emails:denial_user.html.twig */
class __TwigTemplate_481f8d219d9b46c91e892eb4b93c97f78b4ff01f1099ed5bb7f9621075dd7e2b extends Twig_Template
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
        // line 1
        echo "Votre inscription a été refusée par l'Association Le Cairn.

Pour plus d'informations, veuillez contacter ...
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:denial_user.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:denial_user.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/denial_user.html.twig");
    }
}

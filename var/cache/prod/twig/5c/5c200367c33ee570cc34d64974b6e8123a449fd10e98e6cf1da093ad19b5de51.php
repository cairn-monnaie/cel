<?php

/* CairnUserBundle:Emails:farwell.html.twig */
class __TwigTemplate_5d8a6ea6023380c5c3b642a20bdb99966b15c4690c468e711864eb8808008ec8 extends Twig_Template
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
Votre compte a été supprimé avec succès.

Nous espérons vous revoir bientôt dans le réseau numérique du Cairn.


";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:farwell.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:farwell.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/farwell.html.twig");
    }
}

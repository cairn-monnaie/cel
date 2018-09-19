<?php

/* CairnUserBundle:Emails:reminder_card_activation.html.twig */
class __TwigTemplate_6626210e68c1f9b6f3c21f6d8dfed8455395cb27b2901eb1c8d9e4d0e34f12cc extends Twig_Template
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
    Votre carte de sécurité est toujours en attente d'activation. 
    Il vous reste ";
        // line 4
        echo twig_escape_filter($this->env, ($context["remainingDays"] ?? null), "html", null, true);
        echo " jour(s) pour procéder à l'activation. Pour des raisons de sécurité, votre carte sera automatiquement révoquée une fois ce délai dépassé.

A très bientôt,

Le Cairn,
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:reminder_card_activation.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  27 => 4,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:reminder_card_activation.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/reminder_card_activation.html.twig");
    }
}

<?php

/* CairnUserBundle:Emails:reminder_email_activation.html.twig */
class __TwigTemplate_cacfad1746e3a6c32e6e143a3d153547f5591ae943dd05acf520e83e601049ab extends Twig_Template
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

    Il vous reste ";
        // line 4
        echo twig_escape_filter($this->env, ($context["remainingDays"] ?? null), "html", null, true);
        echo " jour(s) pour procéder à l'activation. Pour des raisons de sécurité, votre compte sera automatiquement supprimé.
    Vous pouvez valider votre adresse mail en cliquant sur ce lien : ";
        // line 5
        echo twig_escape_filter($this->env, ($context["confirmationUrl"] ?? null), "html", null, true);
        echo "

    Une validation de l'équipe administrative sera ensuite nécessaire pour valider définitivement votre compte.

    Le Cairn,

";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:reminder_email_activation.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 5,  27 => 4,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:reminder_email_activation.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/reminder_email_activation.html.twig");
    }
}

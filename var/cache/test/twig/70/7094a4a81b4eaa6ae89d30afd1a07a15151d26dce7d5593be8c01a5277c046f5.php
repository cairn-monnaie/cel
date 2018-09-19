<?php

/* CairnUserBundle:Emails:reminder_card_activation.html.twig */
class __TwigTemplate_84e55b9a6ce2e24329ba41c422615c61914c3eb280355c7e74fa8448d15ffe8c extends Twig_Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Emails:reminder_card_activation.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Emails:reminder_card_activation.html.twig"));

        // line 2
        echo "
    Votre carte de sécurité est toujours en attente d'activation. 
    Il vous reste ";
        // line 4
        echo twig_escape_filter($this->env, (isset($context["remainingDays"]) || array_key_exists("remainingDays", $context) ? $context["remainingDays"] : (function () { throw new Twig_Error_Runtime('Variable "remainingDays" does not exist.', 4, $this->source); })()), "html", null, true);
        echo " jour(s) pour procéder à l'activation. Pour des raisons de sécurité, votre carte sera automatiquement révoquée une fois ce délai dépassé.

A très bientôt,

Le Cairn,
";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

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
        return array (  33 => 4,  29 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Emails/reminder_card_activation.html.twig #}

    Votre carte de sécurité est toujours en attente d'activation. 
    Il vous reste {{remainingDays}} jour(s) pour procéder à l'activation. Pour des raisons de sécurité, votre carte sera automatiquement révoquée une fois ce délai dépassé.

A très bientôt,

Le Cairn,
", "CairnUserBundle:Emails:reminder_card_activation.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/reminder_card_activation.html.twig");
    }
}

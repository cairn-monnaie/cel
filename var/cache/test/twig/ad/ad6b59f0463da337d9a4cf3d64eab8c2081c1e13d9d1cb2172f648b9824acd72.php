<?php

/* CairnUserBundle:Emails:expiration_card.html.twig */
class __TwigTemplate_2d9bb11b534e0bba827918163761d36cc7557aa72e5b69612e5f7c4c2819680a extends Twig_Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Emails:expiration_card.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Emails:expiration_card.html.twig"));

        // line 2
        echo "
    Vous n'avez pas validé votre carte de sécurité Cairn à temps.
    Pour des raisons de sécurité, elle a donc été automatiquement révoquée. Vous ne pourrez pas effectuer les opérations sensibles.

    Vous pouvez en commander une nouvelle sur la plateforme.

    Le Cairn,
";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:expiration_card.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  29 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Emails/expiration_card.html.twig #}

    Vous n'avez pas validé votre carte de sécurité Cairn à temps.
    Pour des raisons de sécurité, elle a donc été automatiquement révoquée. Vous ne pourrez pas effectuer les opérations sensibles.

    Vous pouvez en commander une nouvelle sur la plateforme.

    Le Cairn,
", "CairnUserBundle:Emails:expiration_card.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/expiration_card.html.twig");
    }
}

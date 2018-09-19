<?php

/* CairnUserBundle:Emails:pending_validation.html.twig */
class __TwigTemplate_3f81148b868a29c769ad419e2921f51838550dc097d74452a922ef5fd428b3d0 extends Twig_Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Emails:pending_validation.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Emails:pending_validation.html.twig"));

        // line 2
        echo "
Merci d'avoir confirmé la validité de votre adresse email.
Les différentes informations liées à votre activité sur la plateforme vous parviendront à cette adresse.

Un email vous sera envoyé dès lors que l'équipe administrative aura validé votre inscription.
";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:pending_validation.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  29 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Emails/pending_validation.html.twig #}

Merci d'avoir confirmé la validité de votre adresse email.
Les différentes informations liées à votre activité sur la plateforme vous parviendront à cette adresse.

Un email vous sera envoyé dès lors que l'équipe administrative aura validé votre inscription.
", "CairnUserBundle:Emails:pending_validation.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/pending_validation.html.twig");
    }
}

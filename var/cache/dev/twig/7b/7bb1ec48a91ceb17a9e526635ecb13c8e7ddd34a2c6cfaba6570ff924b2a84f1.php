<?php

/* CairnUserBundle:Emails:revoke_card.html.twig */
class __TwigTemplate_d5ff28922b5c65345aff14dc2de8eb282330d4790304fbba531a12f807f2fb48 extends Twig_Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Emails:revoke_card.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Emails:revoke_card.html.twig"));

        // line 2
        echo "
    Une révocation de votre carte de clés de sécurité Cairn a été executée par ";
        // line 3
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["by"]) || array_key_exists("by", $context) ? $context["by"] : (function () { throw new Twig_Error_Runtime('Variable "by" does not exist.', 3, $this->source); })()), "name", array()), "html", null, true);
        echo ".

    N'hésitez pas à contacter l'Association pour plus d'explications.

Le Cairn,
";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:revoke_card.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  32 => 3,  29 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Emails/revoke_card.html.twig #}

    Une révocation de votre carte de clés de sécurité Cairn a été executée par {{by.name}}.

    N'hésitez pas à contacter l'Association pour plus d'explications.

Le Cairn,
", "CairnUserBundle:Emails:revoke_card.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/revoke_card.html.twig");
    }
}

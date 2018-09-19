<?php

/* CairnUserBundle:Emails:new_card.html.twig */
class __TwigTemplate_b5fb325712319e24446ce67c392ed31308b9f17bb1a62e35c5bd48c14df7f37c extends Twig_Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Emails:new_card.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Emails:new_card.html.twig"));

        // line 2
        echo "
    Une nouvelle carte de clés de sécurité a été commandée par ";
        // line 3
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["by"]) || array_key_exists("by", $context) ? $context["by"] : (function () { throw new Twig_Error_Runtime('Variable "by" does not exist.', 3, $this->source); })()), "name", array()), "html", null, true);
        echo ".
    Elle sera envoyée à l'adresse suivante dans les jours à venir : </br>

    ";
        // line 6
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 6, $this->source); })()), "address", array()), "street", array()), "html", null, true);
        echo " </br>  
    ";
        // line 7
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, (isset($context["user"]) || array_key_exists("user", $context) ? $context["user"] : (function () { throw new Twig_Error_Runtime('Variable "user" does not exist.', 7, $this->source); })()), "city", array()), "html", null, true);
        echo " </br>      
    Il vous faudra ensuite valider cette nouvelle carte sous ";
        // line 8
        echo twig_escape_filter($this->env, (isset($context["cairn_card_activation_delay"]) || array_key_exists("cairn_card_activation_delay", $context) ? $context["cairn_card_activation_delay"] : (function () { throw new Twig_Error_Runtime('Variable "cairn_card_activation_delay" does not exist.', 8, $this->source); })()), "html", null, true);
        echo " jours à compter d'aujourd'hui. Le cas échéant, pour des raisons de sécurité, votre carte sera automatiquement révoquée.


";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:new_card.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  46 => 8,  42 => 7,  38 => 6,  32 => 3,  29 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Emails/new_card.html.twig #}

    Une nouvelle carte de clés de sécurité a été commandée par {{by.name}}.
    Elle sera envoyée à l'adresse suivante dans les jours à venir : </br>

    {{user.address.street}} </br>  
    {{user.city}} </br>      
    Il vous faudra ensuite valider cette nouvelle carte sous {{cairn_card_activation_delay}} jours à compter d'aujourd'hui. Le cas échéant, pour des raisons de sécurité, votre carte sera automatiquement révoquée.


", "CairnUserBundle:Emails:new_card.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/new_card.html.twig");
    }
}

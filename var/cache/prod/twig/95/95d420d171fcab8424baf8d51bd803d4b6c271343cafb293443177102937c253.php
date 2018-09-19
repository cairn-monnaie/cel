<?php

/* CairnUserBundle:Emails:new_card.html.twig */
class __TwigTemplate_2d52a6d7996b7f6c32da812f5a222383bc4d2c332ff7044a0dc26fc47e617fad extends Twig_Template
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
    Une nouvelle carte de clés de sécurité a été commandée par ";
        // line 3
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["by"] ?? null), "name", array()), "html", null, true);
        echo ".
    Elle sera envoyée à l'adresse suivante dans les jours à venir : </br>

    ";
        // line 6
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "address", array()), "street", array()), "html", null, true);
        echo " </br>  
    ";
        // line 7
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "city", array()), "html", null, true);
        echo " </br>      
    Il vous faudra ensuite valider cette nouvelle carte sous ";
        // line 8
        echo twig_escape_filter($this->env, ($context["cairn_card_activation_delay"] ?? null), "html", null, true);
        echo " jours à compter d'aujourd'hui. Le cas échéant, pour des raisons de sécurité, votre carte sera automatiquement révoquée.


";
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
        return array (  40 => 8,  36 => 7,  32 => 6,  26 => 3,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:new_card.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/new_card.html.twig");
    }
}

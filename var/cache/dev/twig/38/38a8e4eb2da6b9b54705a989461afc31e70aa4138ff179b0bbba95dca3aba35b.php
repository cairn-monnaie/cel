<?php

/* CairnUserBundle:Card:warning_card_tries.html.twig */
class __TwigTemplate_9d936d20b68aedd1c3e1c64211fc2ea87464534120a9655bcc4c9fe9d96d9fc3 extends Twig_Template
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
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Card:warning_card_tries.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new Twig_Profiler_Profile($this->getTemplateName(), "template", "CairnUserBundle:Card:warning_card_tries.html.twig"));

        // line 1
        echo "         

    ";
        // line 3
        $context["alreadyTried"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["card"]) || array_key_exists("card", $context) ? $context["card"] : (function () { throw new Twig_Error_Runtime('Variable "card" does not exist.', 3, $this->source); })()), "user", array()), "cardKeyTries", array());
        echo " 
    ";
        // line 4
        if (((isset($context["alreadyTried"]) || array_key_exists("alreadyTried", $context) ? $context["alreadyTried"] : (function () { throw new Twig_Error_Runtime('Variable "alreadyTried" does not exist.', 4, $this->source); })()) != 0)) {
            // line 5
            echo "        ";
            $context["remaining"] = (3 - (isset($context["alreadyTried"]) || array_key_exists("alreadyTried", $context) ? $context["alreadyTried"] : (function () { throw new Twig_Error_Runtime('Variable "alreadyTried" does not exist.', 5, $this->source); })()));
            // line 6
            echo "        <strong>Attention : </strong> Il vous reste ";
            echo twig_escape_filter($this->env, (isset($context["remaining"]) || array_key_exists("remaining", $context) ? $context["remaining"] : (function () { throw new Twig_Error_Runtime('Variable "remaining" does not exist.', 6, $this->source); })()), "html", null, true);
            echo " tentative(s) possible(s). Votre compte sera ensuite bloqué en cas d'échec.</br>
    ";
        }
        // line 8
        echo "
";
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Card:warning_card_tries.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  48 => 8,  42 => 6,  39 => 5,  37 => 4,  33 => 3,  29 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("{# src/Cairn/UserBundle/Resources/views/Card/warning_card_tries.html.twig #}         

    {% set alreadyTried = card.user.cardKeyTries %} 
    {% if alreadyTried != 0 %}
        {% set remaining = 3 - alreadyTried %}
        <strong>Attention : </strong> Il vous reste {{remaining}} tentative(s) possible(s). Votre compte sera ensuite bloqué en cas d'échec.</br>
    {% endif %}

", "CairnUserBundle:Card:warning_card_tries.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Card/warning_card_tries.html.twig");
    }
}

<?php

/* CairnUserBundle:Card:warning_card_tries.html.twig */
class __TwigTemplate_b6a3bc669d7d4298079f1e94099722862c3f456fcf97d5a1583ce87ee56108be extends Twig_Template
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
        // line 1
        echo "         

    ";
        // line 3
        $context["alreadyTried"] = twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["card"] ?? null), "user", array()), "cardKeyTries", array());
        echo " 
    ";
        // line 4
        if ((($context["alreadyTried"] ?? null) != 0)) {
            // line 5
            echo "        ";
            $context["remaining"] = (3 - ($context["alreadyTried"] ?? null));
            // line 6
            echo "        <strong>Attention : </strong> Il vous reste ";
            echo twig_escape_filter($this->env, ($context["remaining"] ?? null), "html", null, true);
            echo " tentative(s) possible(s). Votre compte sera ensuite bloqué en cas d'échec.</br>
    ";
        }
        // line 8
        echo "
";
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
        return array (  42 => 8,  36 => 6,  33 => 5,  31 => 4,  27 => 3,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Card:warning_card_tries.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Card/warning_card_tries.html.twig");
    }
}

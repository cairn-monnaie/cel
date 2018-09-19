<?php

/* CairnUserBundle:Emails:revoke_card.html.twig */
class __TwigTemplate_cb2cda942fbf40baa294843ec6f09139ba5cf4a901558dded9263fd7b250b781 extends Twig_Template
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
    Une révocation de votre carte de clés de sécurité Cairn a été executée par ";
        // line 3
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["by"] ?? null), "name", array()), "html", null, true);
        echo ".

    N'hésitez pas à contacter l'Association pour plus d'explications.

Le Cairn,
";
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
        return array (  26 => 3,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:revoke_card.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/revoke_card.html.twig");
    }
}

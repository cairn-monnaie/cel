<?php

/* CairnUserBundle:Emails:account_removal.html.twig */
class __TwigTemplate_57d333973e20f3fc2b97436ef293aaaf5ba860a2693c27e211630cb40d4f3723 extends Twig_Template
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
Suite au comité de pilotage, la décision a été prise de supprimer le compte ";
        // line 3
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "name", array()), "html", null, true);
        echo " de la plateforme. 
Votre liste de bénéficiaires à été mise à jour en conséquence.

Si vous avez des virements automatiques en attente, ils ont été annulés.

Le Cairn,
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:account_removal.html.twig";
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
        return new Twig_Source("", "CairnUserBundle:Emails:account_removal.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/account_removal.html.twig");
    }
}

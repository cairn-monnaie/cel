<?php

/* CairnUserBundle:Emails:account_alert_removal.html.twig */
class __TwigTemplate_928639c17a838cf2976144170bccf5857ededca8f83c3ba21a37d2bf0d642b3c extends Twig_Template
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
Si vous êtes un professionnel, merci d'équilibrer le solde de votre compte dans les plus brefs délais en :
        _effectuant un retrait de Cairns équivalent à votre solde
        _les transferant sur un autre de vos comptes

Attention :  vos virements automatiques à venir, ils seront automatiquement annulés.
 
Le Cairn,
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:account_alert_removal.html.twig";
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
        return new Twig_Source("", "CairnUserBundle:Emails:account_alert_removal.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/account_alert_removal.html.twig");
    }
}

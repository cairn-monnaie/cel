<?php

/* CairnUserBundle:Emails:account_creation.html.twig */
class __TwigTemplate_efd9eec16bb5ff3168bc329d08e760394c787810cf7d7863df6648304388064c extends Twig_Template
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
Suite au comité de pilotage, la décision a été prise de créer le compte ";
        // line 3
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["account"] ?? null), "name", array()), "html", null, true);
        echo " . 
Vous pouvez désormais effectuer toutes les opérations de compte habituelles avec ce compte.
 
Le Cairn,
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:account_creation.html.twig";
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
        return new Twig_Source("", "CairnUserBundle:Emails:account_creation.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/account_creation.html.twig");
    }
}

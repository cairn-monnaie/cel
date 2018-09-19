<?php

/* CairnUserBundle:Emails:dissociation_cyclos_doctrine.html.twig */
class __TwigTemplate_e1cf8136e90b6ee4a5cf846ab2368d9b63ba24667c4597f92587c34f8be3408d extends Twig_Template
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
    Une dissociation a eu lieu entre une entité Doctrine et son équivalent Cyclos.
    Entité en question : ";
        // line 4
        echo twig_escape_filter($this->env, ($context["entity_class"] ?? null), "html", null, true);
        echo "
    ID Doctrine : ";
        // line 5
        echo twig_escape_filter($this->env, ($context["doctrine_id"] ?? null), "html", null, true);
        echo "
    ID Cyclos : ";
        // line 6
        echo twig_escape_filter($this->env, ($context["cyclos_id"] ?? null), "html", null, true);
        echo "

    Contexte : 
        Membre : ";
        // line 9
        echo twig_escape_filter($this->env, ($context["currentUser"] ?? null), "html", null, true);
        echo "
        Trace : ";
        // line 10
        echo twig_escape_filter($this->env, ($context["trace"] ?? null), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:dissociation_cyclos_doctrine.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 10,  41 => 9,  35 => 6,  31 => 5,  27 => 4,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:dissociation_cyclos_doctrine.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/dissociation_cyclos_doctrine.html.twig");
    }
}

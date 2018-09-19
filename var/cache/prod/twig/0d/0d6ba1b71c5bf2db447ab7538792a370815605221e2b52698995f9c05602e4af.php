<?php

/* CairnUserBundle:Emails:new_password.html.twig */
class __TwigTemplate_74f19295c1d256e9c2fd1fe665fb415c327e8b7328a39ca8acd8ef9e56f87fc2 extends Twig_Template
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
    Voici vos nouveaux identifiants de connexion : 

    Identifiant : ";
        // line 5
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "username", array()), "html", null, true);
        echo "
    Votre nouveau mot de passe : ";
        // line 6
        echo twig_escape_filter($this->env, ($context["password"] ?? null), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:new_password.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  32 => 6,  28 => 5,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:new_password.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/new_password.html.twig");
    }
}

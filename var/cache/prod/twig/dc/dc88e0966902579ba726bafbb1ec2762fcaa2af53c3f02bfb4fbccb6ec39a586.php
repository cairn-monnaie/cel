<?php

/* CairnUserBundle:Emails:welcome.html.twig */
class __TwigTemplate_996f32d49161ba7f84819d9cdd86fc7adcf6c40b0a1ab0228a846feb12c2c101 extends Twig_Template
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
Bonjour ";
        // line 3
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "name", array()), "html", null, true);
        echo ",

Bienvenue dans le réseau du Cairn.

  <p>
    <a href=\"http://localhost:8000/login\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-chevron-left\"></i>
      Cliquez sur ce lien pour accéder à la plateforme.
    </a>
      Vous pouvez dès à présent vous connecter avec les identifiants suivants :
      <li> identifiant  : ";
        // line 13
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "username", array()), "html", null, true);
        echo " </li>
      <li> Mot de passe : ";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "plainPassword", array()), "html", null, true);
        echo " </li>
  </p>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:welcome.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  43 => 14,  39 => 13,  26 => 3,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:welcome.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/welcome.html.twig");
    }
}

<?php

/* CairnUserBundle:Emails:submit_alert.html.twig */
class __TwigTemplate_f75151790982cf734c7382789d387b2e749784c1743160382a2b4f9c1b47f107 extends Twig_Template
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
Nouvelle inscription en attente de validation.

  <p>
      Voici les informations fournies à l'inscription :
      <li> nom  : ";
        // line 7
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "name", array()), "html", null, true);
        echo " </li>
      <li> identifiant  : ";
        // line 8
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "username", array()), "html", null, true);
        echo " </li>
      <li> email  : ";
        // line 9
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "email", array()), "html", null, true);
        echo " </li>
      <li> adresse  :
             ";
        // line 11
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "address", array()), "street", array()), "html", null, true);
        echo " </li>
             ";
        // line 12
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "address", array()), "zipCity", array()), "zipCode", array()), "html", null, true);
        echo " </li>
             ";
        // line 13
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "address", array()), "zipCity", array()), "city", array()), "html", null, true);
        echo " </li>

    <a href=\"";
        // line 15
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getUrl("cairn_user_profile_view", array("id" => twig_get_attribute($this->env, $this->source, ($context["user"] ?? null), "id", array()))), "html", null, true);
        echo "\" class=\"btn btn-default\">

      <i class=\"glyphicon glyphicon-chevron-left\"></i>
      Cliquez sur ce lien pour voir le profil du nouvel inscrit.
    </a>

  </p>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Emails:submit_alert.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  56 => 15,  51 => 13,  47 => 12,  43 => 11,  38 => 9,  34 => 8,  30 => 7,  23 => 2,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Emails:submit_alert.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Emails/submit_alert.html.twig");
    }
}

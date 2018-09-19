<?php

/* CairnUserBundle:Banknote:view.html.twig */
class __TwigTemplate_86c5d025b67c71d0e5eb3cc9ae3e4702343b29a00102ef8c3e595612936282d3 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserBundle:Banknote:view.html.twig", 3);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'fos_user_content' => array($this, 'block_fos_user_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "CairnUserCyclosBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        // line 6
        echo "  Réseau du Cairn - ";
        $this->displayParentBlock("title", $context, $blocks);
        echo "
";
    }

    // line 9
    public function block_fos_user_content($context, array $blocks = array())
    {
        // line 10
        echo "
  <h2> Etat du billet </h2>

  <div class=\"well\">
<li> ";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["banknote"] ?? null), "number", array()), "html", null, true);
        echo " </li>    
<li> ";
        // line 15
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["banknote"] ?? null), "value", array()), "html", null, true);
        echo " </li>
<li> Dernière modification : ";
        // line 16
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["banknote"] ?? null), "status", array()), "lastUpdate", array()), "Y-m-d H:i:s"), "html", null, true);
        echo " </li>
<li> Actuellement : ";
        // line 17
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["banknote"] ?? null), "status", array()), "status", array()), "html", null, true);
        echo " </li>
<li> Dernier point de passage : ";
        // line 18
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["banknote"] ?? null), "status", array()), "exchangeOffice", array()), "name", array()), "html", null, true);
        echo " </li>

  </div>

  <p>
     <a href=\"";
        // line 23
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banknote_edit", array("id" => twig_get_attribute($this->env, $this->source, ($context["banknote"] ?? null), "id", array()))), "html", null, true);
        echo "\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-edit\"></i>
      Mettre à jour le billet
    </a>
     <a href=\"";
        // line 27
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_banknote_home");
        echo "\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-trash\"></i>
      Retour à la gestion des billets 
    </a>
  </p>

";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:Banknote:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  83 => 27,  76 => 23,  68 => 18,  64 => 17,  60 => 16,  56 => 15,  52 => 14,  46 => 10,  43 => 9,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:Banknote:view.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/Banknote/view.html.twig");
    }
}

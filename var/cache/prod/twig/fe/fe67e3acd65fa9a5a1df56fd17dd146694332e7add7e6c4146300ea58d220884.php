<?php

/* CairnUserCyclosBundle:Config/Currency:list.html.twig */
class __TwigTemplate_940c21194b1e3149af01a3ad9388df687a3d48df5c7e36fd2ce1a56b7607ccd3 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/Currency:list.html.twig", 3);
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
        echo "  Accueil - ";
        $this->displayParentBlock("title", $context, $blocks);
        echo "
";
    }

    // line 9
    public function block_fos_user_content($context, array $blocks = array())
    {
        // line 10
        echo "
  <h2>Liste des devises du réseau</h2>

  <ul>
    ";
        // line 14
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["listCurrencies"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["currency"]) {
            // line 15
            echo "      <li>
        <a href=\"";
            // line 16
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_currency_view", array("id" => twig_get_attribute($this->env, $this->source, $context["currency"], "id", array()))), "html", null, true);
            echo "\">
          ";
            // line 17
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["currency"], "name", array()), "html", null, true);
            echo "
        </a>
      </li>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 21
            echo "      <li>Pas (encore !) de devise</li>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['currency'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 23
        echo "  </ul>

  <p>
    <a href=\"";
        // line 26
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_home");
        echo "\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-left\"></i>
      Retourner à la configuration du réseau
    </a>

    <a href=\"";
        // line 31
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_currency_add");
        echo "\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-edit\"></i>
      Ajouter une devise
    </a>


  </p>

";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/Currency:list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  93 => 31,  85 => 26,  80 => 23,  73 => 21,  64 => 17,  60 => 16,  57 => 15,  52 => 14,  46 => 10,  43 => 9,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/Currency:list.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/Currency/list.html.twig");
    }
}

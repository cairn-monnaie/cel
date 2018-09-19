<?php

/* CairnUserCyclosBundle:Config/TransferFee:list.html.twig */
class __TwigTemplate_56ba63034f2491085547552818907c4dd056d15979e7e9c2793864717b1d51e8 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/TransferFee:list.html.twig", 3);
        $this->blocks = array(
            'body' => array($this, 'block_body'),
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

    // line 6
    public function block_body($context, array $blocks = array())
    {
        // line 7
        echo "
  <h2>Liste des types de frais</h2>

  <ul>
    ";
        // line 11
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["listTransferFees"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["transferFee"]) {
            // line 12
            echo "      <li>
        <a href=\"";
            // line 13
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_transferfee_view", array("id" => twig_get_attribute($this->env, $this->source, $context["transferFee"], "id", array()))), "html", null, true);
            echo "\">
          ";
            // line 14
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["transferFee"], "name", array()), "html", null, true);
            echo "
        </a>
      </li>
    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 18
            echo "      <li>Pas (encore !) de type de frais de transfert</li>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['transferFee'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 20
        echo "  </ul>

  <p>
    <a href=\"";
        // line 23
        echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_accounttype_view", array("id" => twig_get_attribute($this->env, $this->source, ($context["fromAccountType"] ?? null), "id", array()))), "html", null, true);
        echo "\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-left\"></i>
      Retourner à la gestion du compte
    </a>

    <a href=\"";
        // line 28
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_cyclos_accountsconfig_transferfee_add");
        echo "\" class=\"btn btn-default\">
      <i class=\"glyphicon glyphicon-edit\"></i>
      Ajouter un type de frais de transfert
    </a>


  </p>

";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/TransferFee:list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  82 => 28,  74 => 23,  69 => 20,  62 => 18,  53 => 14,  49 => 13,  46 => 12,  41 => 11,  35 => 7,  32 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/TransferFee:list.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/TransferFee/list.html.twig");
    }
}

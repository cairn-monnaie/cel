<?php

/* CairnUserBundle:BankConnection:index.html.twig */
class __TwigTemplate_9dd156b8efa5ba7a499082e5c9283c77a0f84b3750a0cd06af74f4e656754635 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserBundle:BankConnection:index.html.twig", 3);
        $this->blocks = array(
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
    public function block_fos_user_content($context, array $blocks = array())
    {
        // line 6
        echo "
  <ul>
    ";
        // line 8
        $context["cmpt"] = 1;
        // line 9
        echo "    ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["deposits"] ?? null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["deposit"]) {
            // line 10
            echo "      <li>
        <a href=\"";
            // line 11
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("cairn_user_bankconnection_deposit_view", array("id" => twig_get_attribute($this->env, $this->source, $context["deposit"], "id", array()))), "html", null, true);
            echo "\">
          Dépôt ";
            // line 12
            echo twig_escape_filter($this->env, ($context["cmpt"] ?? null), "html", null, true);
            echo "        
        </a>
        effectué le ";
            // line 14
            echo twig_escape_filter($this->env, twig_date_format_filter($this->env, twig_get_attribute($this->env, $this->source, $context["deposit"], "date", array()), "Y-m-d"), "html", null, true);
            echo "
      </li>
     ";
            // line 16
            $context["cmpt"] = (($context["cmpt"] ?? null) + 1);
            // line 17
            echo "    ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 18
            echo "      <li>Pas (encore !) de dépôt effectué</li>

    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['deposit'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 21
        echo "  </ul>

";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:BankConnection:index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  79 => 21,  71 => 18,  66 => 17,  64 => 16,  59 => 14,  54 => 12,  50 => 11,  47 => 10,  41 => 9,  39 => 8,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:BankConnection:index.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/BankConnection/index.html.twig");
    }
}

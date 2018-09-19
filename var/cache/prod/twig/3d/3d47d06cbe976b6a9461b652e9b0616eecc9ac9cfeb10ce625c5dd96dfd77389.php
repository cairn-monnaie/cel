<?php

/* CairnUserCyclosBundle:Config/TransferFee:view.html.twig */
class __TwigTemplate_3b67739ced16dc3af53db0916df2ae5d54b21a86ef867818eb7dd165c2a9e410 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/TransferFee:view.html.twig", 3);
        $this->blocks = array(
            'title' => array($this, 'block_title'),
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
    public function block_body($context, array $blocks = array())
    {
        // line 10
        echo "
  <h2>";
        // line 11
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["transferFee"] ?? null), "name", array()), "html", null, true);
        echo "</h2>

  <div class=\"well\">
<li>";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["transferFee"] ?? null), "originalTransferType", array()), "name", array()), "html", null, true);
        echo "</li>
<li>";
        // line 15
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["transferFee"] ?? null), "chargeMode", array()), "html", null, true);
        echo "</li>
<li>";
        // line 16
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["transferFee"] ?? null), "amount", array()), "html", null, true);
        echo "</li>
<li>";
        // line 17
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["transferFee"] ?? null), "enabled", array()), "html", null, true);
        echo "</li>
<ul>
  ";
        // line 19
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["transferFee"] ?? null), "fromGroups", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["group"]) {
            // line 20
            echo "    <li>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["group"], "name", array()), "html", null, true);
            echo "</li>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['group'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 22
        echo "</ul>
<ul>
  ";
        // line 24
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, ($context["transferFee"] ?? null), "toGroups", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["group"]) {
            // line 25
            echo "    <li>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["group"], "name", array()), "html", null, true);
            echo "</li>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['group'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 27
        echo "</ul>

<li>";
        // line 29
        echo "</li>
<li>";
        // line 30
        echo "</li>

  </div>


";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/TransferFee:view.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  109 => 30,  106 => 29,  102 => 27,  93 => 25,  89 => 24,  85 => 22,  76 => 20,  72 => 19,  67 => 17,  63 => 16,  59 => 15,  55 => 14,  49 => 11,  46 => 10,  43 => 9,  36 => 6,  33 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/TransferFee:view.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/TransferFee/view.html.twig");
    }
}

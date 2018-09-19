<?php

/* CairnUserBundle:BankConnection:confirm_deposit.html.twig */
class __TwigTemplate_0bbf04cc6b411410b99301283e03983dba89c2d7a8171eb7cc8d657a849c4f4d extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserBundle:BankConnection:confirm_deposit.html.twig", 3);
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
<h3> Récapitulatif du dépôt en cours</h3>
   <li> ";
        // line 8
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["deposit"] ?? null), "nbEuros", array()), "html", null, true);
        echo "€      </li>  
   <li> ";
        // line 9
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["deposit"] ?? null), "nbCairns", array()), "html", null, true);
        echo " cairns</li>
   <li>Depuis le comptoir de change : ";
        // line 10
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["deposit"] ?? null), "exchangeOffice", array()), "html", null, true);
        echo "</li>
<div class=\"well\">
  ";
        // line 12
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form');
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "CairnUserBundle:BankConnection:confirm_deposit.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 12,  47 => 10,  43 => 9,  39 => 8,  35 => 6,  32 => 5,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserBundle:BankConnection:confirm_deposit.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserBundle/Resources/views/BankConnection/confirm_deposit.html.twig");
    }
}

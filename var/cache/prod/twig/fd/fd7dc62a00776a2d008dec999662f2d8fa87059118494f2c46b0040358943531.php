<?php

/* CairnUserCyclosBundle:Config/AccountType:confirm_unassign.html.twig */
class __TwigTemplate_b8b885931a59a72a5feb3e4c11368ed29b8c887d113eae7eb5c4d0dba30cb920 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        // line 3
        $this->parent = $this->loadTemplate("CairnUserCyclosBundle::layout.html.twig", "CairnUserCyclosBundle:Config/AccountType:confirm_unassign.html.twig", 3);
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
        echo "    ";
        $this->displayParentBlock("body", $context, $blocks);
        echo "
  <h2>";
        // line 8
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["accountType"] ?? null), "name", array()), "html", null, true);
        echo "</h2>

  <div class=\"well\">
    <div>
        ";
        // line 12
        if (($context["assign"] ?? null)) {
            // line 13
            echo "            Attention, ce compte sera accessible par tous les professionnels.
            <div id=\"confirm_action\"> <em>Confirmer l'ouverture du compte </em>

        ";
        } else {
            // line 17
            echo "            Attention, ce compte est accessible par tous les professionnels. Le fermer ne le supprime pas définitivement, mais le rend invisible.
             <div id=\"confirm_action\"> <em>Confirmer la fermeture d'accès à ce compte </em>

        ";
        }
        // line 21
        echo "             ";
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_start');
        echo "                                             
             ";
        // line 22
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'rest');
        echo "                                              
             ";
        // line 23
        echo         $this->env->getRuntime('Symfony\Bridge\Twig\Form\TwigRenderer')->renderBlock(($context["form"] ?? null), 'form_end');
        echo "                                                
         </div> 

";
    }

    public function getTemplateName()
    {
        return "CairnUserCyclosBundle:Config/AccountType:confirm_unassign.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  70 => 23,  66 => 22,  61 => 21,  55 => 17,  49 => 13,  47 => 12,  40 => 8,  35 => 7,  32 => 6,  15 => 3,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "CairnUserCyclosBundle:Config/AccountType:confirm_unassign.html.twig", "/var/www/Symfony/CairnB2B/src/Cairn/UserCyclosBundle/Resources/views/Config/AccountType/confirm_unassign.html.twig");
    }
}
